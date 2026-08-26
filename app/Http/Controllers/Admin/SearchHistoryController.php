<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchHistory;
use Illuminate\Http\Request;

class SearchHistoryController extends Controller
{
    /**
     * Display search history records.
     */
    public function index(Request $request)
    {
        $query = SearchHistory::with(['user', 'wasteItem.category']);

        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->whereHas('wasteItem', function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%");
            })->orWhereHas('user', function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('email', 'LIKE', "%{$term}%");
            });
        }

        $histories = $query->orderBy('searched_at', 'desc')->get();

        return view('admin.search_history.index', compact('histories'));
    }

    /**
     * Remove a search history entry.
     */
    public function destroy(SearchHistory $searchHistory)
    {
        $searchHistory->delete();

        return redirect()->route('admin.search-history.index')
            ->with('success', 'Search history entry deleted successfully!');
    }
}
