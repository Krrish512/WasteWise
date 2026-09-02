<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WasteSearchController;
use App\Http\Controllers\Api\SearchTrendsController;
use App\Http\Controllers\Api\CollectionCentreImportController;

/*
|--------------------------------------------------------------------------
| API Routes — WasteWise
|--------------------------------------------------------------------------
|
| All routes here are automatically prefixed with /api.
| Assigned "web" middleware group to enable session-based user authentication.
|
*/

// ── Waste Search & Recommendation ──────────────────────────────────────────
// GET /api/waste/search?q=Old+Laptop
Route::middleware('web')->get('/waste/search', [WasteSearchController::class, 'search']);

// ── Search Trends for Python Insights Service ──────────────────────────────
// GET /api/search-trends?focus=all|electronic|plastic|organic
Route::get('/search-trends', [SearchTrendsController::class, 'index']);

// ── Protected Admin Collection Centres CSV Import API ───────────────────────
// POST /api/admin/collection-centres/import
Route::middleware(['web', 'admin'])->post('/admin/collection-centres/import', [CollectionCentreImportController::class, 'import']);

