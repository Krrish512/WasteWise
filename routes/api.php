<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WasteSearchController;

/*
|--------------------------------------------------------------------------
| API Routes — WasteWise
|--------------------------------------------------------------------------
|
| All routes here are automatically prefixed with /api
| and assigned the "api" middleware group.
|
*/

// ── Waste Search & Recommendation ──────────────────────────────────────────
// GET /api/waste/search?q=Old+Laptop
Route::get('/waste/search', [WasteSearchController::class, 'search']);
