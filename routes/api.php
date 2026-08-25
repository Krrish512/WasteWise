<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WasteSearchController;

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
