<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CollectionCentre;
use App\Models\DisposalMethod;
use App\Models\Feedback;
use App\Models\SearchHistory;
use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WasteItem;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Dashboard with real database statistics.
     */
    public function index()
    {
        $stats = [
            'waste_items'        => WasteItem::count(),
            'categories'         => WasteCategory::count(),
            'collection_centres' => CollectionCentre::count(),
            'disposal_methods'   => DisposalMethod::count(),
            'cities'             => City::count(),
            'users'              => User::count(),
            'searches_today'     => SearchHistory::whereDate('created_at', Carbon::today())->count(),
            'pending_feedback'   => Feedback::where('is_reviewed', false)->count(),
            'avg_rating'         => round((float) (Feedback::avg('rating') ?? 0), 1),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
