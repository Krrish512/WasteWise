<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CollectionCentre;
use App\Models\WasteCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CollectionCentreImportController extends Controller
{
    /**
     * POST /api/admin/collection-centres/import
     *
     * Protected bulk import API endpoint for Collection Centre records.
     * Validates every record, verifies category/city existence, handles duplicates by updating,
     * uses database transactions (all-or-nothing rollback on validation failure).
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
                'success'  => false,
                'imported' => 0,
                'updated'  => 0,
                'failed'   => 1,
                'errors'   => [
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

        // Pre-fetch all cities and categories for case-insensitive lookup
        $cities = City::all()->keyBy(fn ($c) => strtolower(trim($c->name)));
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

            $validator = Validator::make($row, [
                'name'      => 'required|string|max:255',
                'category'  => 'required|string',
                'city'      => 'required|string',
                'address'   => 'required|string|max:1000',
                'phone'     => 'nullable|string|max:50',
                'latitude'  => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
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

            // Lookup City
            $cityName = strtolower(trim($data['city']));
            if (!$cities->has($cityName)) {
                $errors[] = [
                    'row'     => $rowNum,
                    'field'   => 'city',
                    'message' => "City '{$data['city']}' not found in database.",
                ];
            } else {
                $data['city_id'] = $cities->get($cityName)->id;
            }

            // Lookup WasteCategory
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

        // Transaction & All-or-Nothing Rollback:
        // If ANY row has validation/lookup errors, do NOT import any rows.
        if (!empty($errors)) {
            return response()->json([
                'success'  => false,
                'imported' => 0,
                'updated'  => 0,
                'failed'   => count($errors),
                'errors'   => $errors,
            ], 422);
        }

        $importedCount = 0;
        $updatedCount = 0;

        DB::transaction(function () use ($validatedRows, &$importedCount, &$updatedCount) {
            foreach ($validatedRows as $row) {
                // Safest business key: name + city_id + address
                $existing = CollectionCentre::where('name', $row['name'])
                    ->where('city_id', $row['city_id'])
                    ->where('address', $row['address'])
                    ->first();

                if ($existing) {
                    $existing->update([
                        'category_id' => $row['category_id'],
                        'phone'       => $row['phone'] ?? $existing->phone,
                        'latitude'    => $row['latitude'] ?? $existing->latitude,
                        'longitude'   => $row['longitude'] ?? $existing->longitude,
                    ]);
                    $updatedCount++;
                } else {
                    CollectionCentre::create([
                        'name'        => $row['name'],
                        'city_id'     => $row['city_id'],
                        'category_id' => $row['category_id'],
                        'address'     => $row['address'],
                        'phone'       => $row['phone'] ?? null,
                        'latitude'    => $row['latitude'] ?? null,
                        'longitude'   => $row['longitude'] ?? null,
                    ]);
                    $importedCount++;
                }
            }
        });

        return response()->json([
            'success'  => true,
            'imported' => $importedCount,
            'updated'  => $updatedCount,
            'failed'   => 0,
        ], 200);
    }
}
