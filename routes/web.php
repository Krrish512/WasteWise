<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\AnnouncementController;
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
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\SustainabilityTipController as AdminSustainabilityTipController;
use App\Http\Controllers\Admin\KnowledgeArticleController as AdminKnowledgeArticleController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\SustainabilityTipController;
use App\Http\Controllers\KnowledgeArticleController;
use App\Models\CollectionCentre;
use App\Models\WasteCategory;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $collectionCentres = [];
    $categories = [];

    if (Schema::hasTable('collection_centres')) {
        $collectionCentres = CollectionCentre::with(['city', 'category'])
            ->orderBy('name')
            ->get()
            ->map(function ($centre) {
                return [
                    'id'       => $centre->id,
                    'name'     => $centre->name,
                    'city'     => $centre->city ? $centre->city->name : '',
                    'address'  => $centre->address,
                    'phone'    => $centre->phone,
                    'category' => $centre->category ? $centre->category->name : '',
                    'lat'      => $centre->latitude !== null ? (float) $centre->latitude : null,
                    'lng'      => $centre->longitude !== null ? (float) $centre->longitude : null,
                ];
            });
    }

    if (Schema::hasTable('waste_categories')) {
        $categories = WasteCategory::orderBy('name')->get();
    }

    return view('welcome', compact('collectionCentres', 'categories'));
})->name('home');

// ── Public User Feedback Routes ─────────────────────────────────────────────
Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

// ── Public Announcements / Updates Routes ─────────────────────────────────
Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/announcements/{announcement:slug}', [AnnouncementController::class, 'show'])->name('announcements.show');

// ── Public Sustainability Tips Routes ─────────────────────────────────────
Route::get('/sustainability-tips', [SustainabilityTipController::class, 'index'])->name('sustainability-tips.index');
Route::get('/sustainability-tips/{sustainabilityTip:slug}', [SustainabilityTipController::class, 'show'])->name('sustainability-tips.show');

// ── Public Knowledge Hub Routes ───────────────────────────────────────────
Route::get('/knowledge', [KnowledgeArticleController::class, 'index'])->name('knowledge.index');
Route::get('/knowledge/{knowledgeArticle:slug}', [KnowledgeArticleController::class, 'show'])->name('knowledge.show');

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

        // Phase 9: Announcements / Updates CRUD
        Route::resource('announcements', AdminAnnouncementController::class);
        Route::patch('/announcements/{announcement}/toggle-published', [AdminAnnouncementController::class, 'togglePublished'])->name('announcements.toggle-published');

        // Phase 10: Sustainability Tips CRUD
        Route::resource('sustainability-tips', AdminSustainabilityTipController::class);
        Route::patch('/sustainability-tips/{sustainabilityTip}/toggle-published', [AdminSustainabilityTipController::class, 'togglePublished'])->name('sustainability-tips.toggle-published');

        // Phase 11: Waste Knowledge Hub CRUD
        Route::resource('knowledge-articles', AdminKnowledgeArticleController::class);
        Route::patch('/knowledge-articles/{knowledgeArticle}/toggle-published', [AdminKnowledgeArticleController::class, 'togglePublished'])->name('knowledge-articles.toggle-published');

        // Phase 12: Smart Waste Analytics & Python Integration (Unit 4)
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::post('/analytics/generate-report', [AnalyticsController::class, 'generateReport'])->name('analytics.generate-report');
        Route::get('/analytics/view-report', [AnalyticsController::class, 'viewReport'])->name('analytics.view-report');
    });
});
