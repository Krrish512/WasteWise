<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WasteItem;
use Illuminate\Http\Request;

class WasteSearchController extends Controller
{
    /**
     * GET /api/waste/search?q=Old+Laptop
     *
     * Search for a waste item by name or keywords,
     * then return its category, disposal method, and suitable collection centres.
     */
    public function search(Request $request)
    {
        // ── 1. Validate the query parameter ──────────────────────────────────
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $query = trim($validated['q']);

        // ── 2. Search waste_items (exact → partial name → keyword) ──────────
        $item = WasteItem::with(['category', 'disposalMethod'])
            // Exact match first (highest priority)
            ->where('name', $query)
            // Partial name match
            ->orWhere('name', 'LIKE', '%' . $query . '%')
            // Keyword column match (column exists per migration)
            ->orWhere('keywords', 'LIKE', '%' . $query . '%')
            ->first();

        // ── 3. 404 if nothing found ──────────────────────────────────────────
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, we couldn\'t find this waste item. '
                    . 'Try another name such as Old Laptop, Old Mobile, Plastic Bottle, or Newspaper.',
            ], 404);
        }

        // ── 4. Find collection centres that handle this waste category ────────
        $centres = collect();

        if ($item->category) {
            $centres = $item->category
                ->collectionCentres()          // hasMany via category_id
                ->with('city')                 // eager-load city for the name
                ->get()
                ->map(function ($centre) {
                    return [
                        'id'        => $centre->id,
                        'name'      => $centre->name,
                        'city'      => $centre->city ? $centre->city->name : null,
                        'address'   => $centre->address,
                        'phone'     => $centre->phone,
                        'latitude'  => $centre->latitude,
                        'longitude' => $centre->longitude,
                    ];
                });
        }

        // ── 5. Build and return the success response ──────────────────────────
        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $item->id,
                'name'        => $item->name,
                'description' => $item->description,
                'category'    => $item->category ? [
                    'id'   => $item->category->id,
                    'name' => $item->category->name,
                ] : null,
                'disposal_method' => $item->disposalMethod ? [
                    'id'           => $item->disposalMethod->id,
                    'name'         => $item->disposalMethod->name,
                    'instructions' => $item->disposalMethod->instructions,
                    'avoid'        => $item->disposalMethod->avoid,
                ] : null,
                'collection_centres' => $centres,
            ],
        ]);
    }
}
