<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SearchHistory;
use Illuminate\Http\Request;

class SearchTrendsController extends Controller
{
    /**
     * GET /api/search-trends?focus=all|electronic|plastic|organic
     *
     * Return aggregated search history data as JSON for the Flask Python
     * Insights service to consume. Read-only, no authentication required
     * (returns only aggregate non-PII data).
     */
    public function index(Request $request)
    {
        $focus = strtolower(trim($request->query('focus', 'all')));

        $query = SearchHistory::with(['wasteItem.category']);

        // Apply category filter based on focus parameter
        if ($focus !== 'all') {
            $categoryFilter = match ($focus) {
                'electronic' => 'Electronic',
                'plastic'    => 'Plastic',
                'organic'    => 'Organic',
                default      => null,
            };

            if ($categoryFilter) {
                $query->whereHas('wasteItem.category', function ($q) use ($categoryFilter) {
                    $q->where('name', 'LIKE', "%{$categoryFilter}%");
                });
            }
        }

        $records = $query->orderBy('searched_at', 'desc')->get();

        $mapped = $records->map(function ($record) {
            return [
                'waste_item' => $record->wasteItem ? $record->wasteItem->name : 'Unknown Item',
                'category'   => ($record->wasteItem && $record->wasteItem->category)
                    ? $record->wasteItem->category->name
                    : 'Uncategorized',
                'searched_at' => $record->searched_at
                    ? (is_string($record->searched_at) ? $record->searched_at : $record->searched_at->format('Y-m-d H:i:s'))
                    : ($record->created_at ? $record->created_at->format('Y-m-d H:i:s') : null),
            ];
        });

        return response()->json([
            'success'        => true,
            'total_searches' => $mapped->count(),
            'focus'          => $focus,
            'records'        => $mapped->values()->toArray(),
        ]);
    }
}
