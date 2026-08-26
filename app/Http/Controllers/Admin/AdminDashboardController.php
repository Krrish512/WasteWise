<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WasteItem;
use App\Models\WasteCategory;
use App\Models\CollectionCentre;
use App\Models\DisposalMethod;
use App\Models\City;
use App\Models\SearchHistory;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Dashboard.
     */
    public function index()
    {
        $stats = [
            'waste_items'        => WasteItem::count(),
            'categories'         => WasteCategory::count(),
            'collection_centres' => CollectionCentre::count(),
            'disposal_methods'   => DisposalMethod::count(),
            'cities'             => City::count(),
            'searches_today'     => SearchHistory::whereDate('created_at', Carbon::today())->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
