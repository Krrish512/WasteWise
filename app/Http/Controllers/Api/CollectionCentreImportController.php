<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CollectionCentre;
use App\Models\WasteCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CollectionCentreImportController extends Controller
{
    /**
     * POST /api/admin/collection-centres/import
     *
     * Protected bulk import API endpoint for Collection Centre records.
     * Features:
     * - Auto-creates missing cities via City::firstOrCreate(['name' => $cityName], ['district' => $district])
     * - Extended 18-column research schema support with backward compatibility
     * - Non-destructive duplicate detection (matching normalized name + city, exact phone, or <=150m Haversine)
     * - Summary counters: inserted, skipped_duplicates, flagged_conflicts
     * - Auto-sets status = 'pending_verification' whenever authorization_valid_until < today() or latitude is null
     */
    public function import(Request $request)
    {
        $payload = $request->json()->all();

        // Support direct array or wrapping object {"centres": [...]}
        $centres = $request->input('centres');
        if (!is_array($centres) && is_array($payload) && array_is_list($payload)) {
            $centres = $payload;
        }

        if (!is_array($centres) || empty($centres)) {
            return response()->json([
                'success'            => false,
                'imported'           => 0,
                'inserted'           => 0,
                'updated'            => 0,
                'skipped_duplicates' => 0,
                'flagged_conflicts'  => 0,
                'failed'             => 1,
                'errors'             => [
                    [
                        'row'     => 0,
                        'field'   => 'centres',
                        'message' => 'Invalid or empty JSON payload. "centres" array is required.',
                    ]
                ],
            ], 422);
        }

        $errors = [];
        $validatedRows = [];

        // Pre-fetch all categories for case-insensitive lookup
        $categories = WasteCategory::all()->keyBy(fn ($cat) => strtolower(trim($cat->name)));

        foreach ($centres as $index => $row) {
            $rowNum = $index + 1;

            if (!is_array($row)) {
                $errors[] = [
                    'row'     => $rowNum,
                    'field'   => 'row',
                    'message' => 'Row must be a valid JSON object.',
                ];
                continue;
            }

            // Map alternate column names
            $mapped = $row;
            if (empty($mapped['name']) && !empty($mapped['centre_name'])) {
                $mapped['name'] = $mapped['centre_name'];
            }
            if (empty($mapped['category']) && !empty($mapped['waste_category'])) {
                $mapped['category'] = $mapped['waste_category'];
            }

            $validator = Validator::make($mapped, [
                'name'                      => 'required|string|max:255',
                'category'                  => 'required|string',
                'city'                      => 'required|string',
                'district'                  => 'nullable|string|max:255',
                'address'                   => 'required|string|max:1000',
                'phone'                     => 'nullable|string|max:50',
                'latitude'                  => 'nullable|numeric|between:-90,90',
                'longitude'                 => 'nullable|numeric|between:-180,180',
                'facility_type'             => 'nullable|string',
                'public_access'             => 'nullable|string',
                'authorization_status'      => 'nullable|string',
                'authorization_id'          => 'nullable|string|max:255',
                'authorization_valid_until' => 'nullable|date',
                'website'                   => 'nullable|string|max:255',
                'source_url'                => 'nullable|string|max:500',
                'verification_date'         => 'nullable|date',
                'confidence'                => 'nullable|string',
                'status'                    => 'nullable|string',
                'notes'                     => 'nullable|string',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->getMessages() as $field => $messages) {
                    foreach ($messages as $msg) {
                        $errors[] = [
                            'row'     => $rowNum,
                            'field'   => $field,
                            'message' => $msg,
                        ];
                    }
                }
                continue;
            }

            $data = $validator->validated();

            // Lookup Category
            $categoryName = strtolower(trim($data['category']));
            if (!$categories->has($categoryName)) {
                $errors[] = [
                    'row'     => $rowNum,
                    'field'   => 'category',
                    'message' => "Waste category '{$data['category']}' not found in database.",
                ];
            } else {
                $data['category_id'] = $categories->get($categoryName)->id;
            }

            $validatedRows[] = $data;
        }

        if (!empty($errors)) {
            return response()->json([
                'success'            => false,
                'imported'           => 0,
                'inserted'           => 0,
                'updated'            => 0,
                'skipped_duplicates' => 0,
                'flagged_conflicts'  => 0,
                'failed'             => count($errors),
                'errors'             => $errors,
            ], 422);
        }

        $insertedCount = 0;
        $skippedDuplicates = 0;
        $flaggedConflicts = 0;

        DB::transaction(function () use ($validatedRows, &$insertedCount, &$skippedDuplicates, &$flaggedConflicts) {
            $allExisting = CollectionCentre::with('city')->get();

            foreach ($validatedRows as $row) {
                // Auto-create missing city via City::firstOrCreate(['name' => $cityName], ['district' => $district])
                $cityName = trim($row['city']);
                $district = !empty($row['district']) ? trim($row['district']) : null;
                $city = City::firstOrCreate(
                    ['name' => $cityName],
                    ['district' => $district, 'state' => $row['state'] ?? 'Gujarat']
                );

                if ($district && empty($city->district)) {
                    $city->update(['district' => $district]);
                }

                $row['city_id'] = $city->id;

                // Non-destructive duplicate detection:
                // 1) Matching normalized name + city
                // 2) Exact phone (if phone non-empty)
                // 3) Within 150m Haversine distance (if both have lat & lng)
                $isDuplicate = false;
                $isConflict = false;

                $normName = strtolower(preg_replace('/[^\w]/', '', $row['name']));
                $normPhone = !empty($row['phone']) ? preg_replace('/[^\d]/', '', $row['phone']) : null;
                $lat1 = isset($row['latitude']) && $row['latitude'] !== null ? (float) $row['latitude'] : null;
                $lng1 = isset($row['longitude']) && $row['longitude'] !== null ? (float) $row['longitude'] : null;

                foreach ($allExisting as $existing) {
                    $existNormName = strtolower(preg_replace('/[^\w]/', '', $existing->name));
                    $existNormPhone = !empty($existing->phone) ? preg_replace('/[^\d]/', '', $existing->phone) : null;
                    $lat2 = $existing->latitude !== null ? (float) $existing->latitude : null;
                    $lng2 = $existing->longitude !== null ? (float) $existing->longitude : null;

                    $nameCityMatch = ($normName === $existNormName && $existing->city_id === $city->id);
                    $phoneMatch = ($normPhone && $existNormPhone && $normPhone === $existNormPhone);
                    $geoMatch = false;

                    if ($lat1 !== null && $lng1 !== null && $lat2 !== null && $lng2 !== null) {
                        $distMeters = $this->haversineDistance($lat1, $lng1, $lat2, $lng2);
                        if ($distMeters <= 150) {
                            $geoMatch = true;
                        }
                    }

                    if ($nameCityMatch || $phoneMatch || $geoMatch) {
                        $isDuplicate = true;
                        // Flag conflict if phone or geo matched but normalized name differs substantially
                        if (!$nameCityMatch && ($phoneMatch || $geoMatch) && $normName !== $existNormName) {
                            $isConflict = true;
                        }
                        break;
                    }
                }

                if ($isDuplicate) {
                    $skippedDuplicates++;
                    if ($isConflict) {
                        $flaggedConflicts++;
                    }
                    continue; // Skip non-destructively
                }

                // Determine lifecycle status:
                // Auto-set status = 'pending_verification' whenever authorization_valid_until < today() or latitude is null
                $status = $row['status'] ?? 'active';
                $validUntil = !empty($row['authorization_valid_until']) ? Carbon::parse($row['authorization_valid_until']) : null;
                if (($validUntil && $validUntil->isPast()) || $lat1 === null || $lng1 === null) {
                    $status = 'pending_verification';
                }

                $newCentre = CollectionCentre::create([
                    'name'                      => $row['name'],
                    'city_id'                   => $city->id,
                    'district'                  => $district,
                    'category_id'               => $row['category_id'],
                    'address'                   => $row['address'],
                    'phone'                     => $row['phone'] ?? null,
                    'latitude'                  => $lat1,
                    'longitude'                 => $lng1,
                    'facility_type'             => $row['facility_type'] ?? 'public_dropoff',
                    'public_access'             => $row['public_access'] ?? 'public_dropoff',
                    'authorization_status'      => $row['authorization_status'] ?? 'verified',
                    'authorization_id'          => $row['authorization_id'] ?? null,
                    'authorization_valid_until' => $row['authorization_valid_until'] ?? null,
                    'website'                   => $row['website'] ?? null,
                    'source_url'                => $row['source_url'] ?? null,
                    'verification_date'         => $row['verification_date'] ?? null,
                    'confidence'                => $row['confidence'] ?? 'medium',
                    'status'                    => $status,
                    'notes'                     => $row['notes'] ?? null,
                ]);

                // Sync pivot table
                $newCentre->categories()->sync([$row['category_id']]);

                // Add to existing collection for intra-batch duplicate checking
                $allExisting->push($newCentre);
                $insertedCount++;
            }
        });

        return response()->json([
            'success'            => true,
            'imported'           => $insertedCount,
            'inserted'           => $insertedCount,
            'updated'            => 0,
            'skipped_duplicates' => $skippedDuplicates,
            'flagged_conflicts'  => $flaggedConflicts,
            'failed'             => 0,
        ], 200);
    }

    /**
     * Calculate Haversine distance between two coordinates in meters.
     */
    protected function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
