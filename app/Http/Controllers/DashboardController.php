<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the authenticated user dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Fetch user's recent search history (max 10, newest first)
        $recentSearches = $user->searchHistories()
            ->with(['wasteItem.category', 'wasteItem.disposalMethod'])
            ->latest('searched_at')
            ->take(10)
            ->get();

        return view('dashboard', [
            'user'           => $user,
            'recentSearches' => $recentSearches,
        ]);
    }
}
