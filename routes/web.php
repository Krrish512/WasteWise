<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DisposalMethodController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CollectionCentreController;
use App\Http\Controllers\Admin\WasteItemController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SearchHistoryController;
use App\Http\Controllers\Admin\AdminFeedbackController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// ── Public User Feedback Routes ─────────────────────────────────────────────
Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/* ── Admin Routes ─────────────────────────────────────────────────────────── */
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Phase 1: Categories CRUD
        Route::resource('categories', CategoryController::class);

        // Phase 2: Disposal Methods CRUD
        Route::resource('disposal-methods', DisposalMethodController::class);

        // Phase 3: Cities CRUD
        Route::resource('cities', CityController::class);

        // Phase 4: Collection Centres CRUD
        Route::resource('collection-centres', CollectionCentreController::class);

        // Phase 5: Waste Items CRUD
        Route::resource('waste-items', WasteItemController::class);

        // Phase 6: User Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Phase 7: Search History
        Route::get('/search-history', [SearchHistoryController::class, 'index'])->name('search-history.index');
        Route::delete('/search-history/{searchHistory}', [SearchHistoryController::class, 'destroy'])->name('search-history.destroy');

        // Phase 8: User Feedback Management
        Route::get('/feedback', [AdminFeedbackController::class, 'index'])->name('feedback.index');
        Route::get('/feedback/{feedback}', [AdminFeedbackController::class, 'show'])->name('feedback.show');
        Route::patch('/feedback/{feedback}/toggle-reviewed', [AdminFeedbackController::class, 'toggleReviewed'])->name('feedback.toggle-reviewed');
        Route::delete('/feedback/{feedback}', [AdminFeedbackController::class, 'destroy'])->name('feedback.destroy');
    });
});
