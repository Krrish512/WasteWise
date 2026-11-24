<?php

namespace Tests\Feature;

use App\Models\SearchHistory;
use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WasteItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $normalUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email'    => 'analyticsadmin@wastewise.com',
            'is_admin' => true,
        ]);

        $this->normalUser = User::factory()->create([
            'email'    => 'normaluser@wastewise.com',
            'is_admin' => false,
        ]);
    }

    public function test_guest_cannot_access_analytics_page(): void
    {
        $response = $this->get('/admin/analytics');
        $response->assertRedirect('/login');
    }

    public function test_normal_authenticated_user_cannot_access_analytics_page(): void
    {
        $response = $this->actingAs($this->normalUser)->get('/admin/analytics');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_analytics_page_with_real_search_data(): void
    {
        $category = WasteCategory::create([
            'name'        => 'Plastic Waste',
            'description' => 'Plastic items',
        ]);

        $disposalMethod = \App\Models\DisposalMethod::create([
            'name'         => 'Recycle',
            'description'  => 'Recycling process',
            'instructions' => 'Follow local recycling guidelines.',
        ]);

        $wasteItem = WasteItem::create([
            'name'               => 'Plastic Bottle',
            'description'        => 'PET Bottle',
            'category_id'        => $category->id,
            'disposal_method_id' => $disposalMethod->id,
        ]);

        SearchHistory::create([
            'user_id'       => $this->normalUser->id,
            'waste_item_id' => $wasteItem->id,
            'searched_at'   => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/analytics');
        $response->assertStatus(200);
        $response->assertSee('Smart Waste Insights');
        $response->assertSee('Plastic Waste');
    }

    public function test_admin_analytics_with_healthy_mocked_flask_service(): void
    {
        Http::fake([
            '*/health' => Http::response(['status' => 'ok', 'service' => 'wastewise-flask'], 200),
            '*/api/insights*' => Http::response([
                'success' => true,
                'status' => 'success',
                'source' => 'flask',
                'total_searches' => 42,
                'top_category' => 'Electronic Waste',
                'top_item' => 'Old Laptop',
                'percentage' => 71.4,
                'most_active_day' => 'Today',
                'category_breakdown' => [
                    ['category' => 'Electronic Waste', 'searches' => 30, 'percentage' => 71.4],
                    ['category' => 'Plastic Waste', 'searches' => 12, 'percentage' => 28.6],
                ],
                'insight' => 'Electronic Waste leads search activity with 30 out of 42 searches.',
            ], 200),
            '*/api/report*' => Http::response([
                'success' => true,
                'source' => 'flask',
                'generated_at' => now()->toIso8601String(),
                'total_searches' => 42,
                'category_breakdown' => ['Electronic Waste' => 30],
            ], 200),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/analytics');
        $response->assertStatus(200);
        $response->assertSee('Python Flask Engine');
        $response->assertSee('Electronic Waste');

        // Test health endpoint
        $healthResponse = $this->actingAs($this->admin)->post('/admin/analytics/test-health');
        $healthResponse->assertRedirect();
        $healthResponse->assertSessionHas('success');
    }

    public function test_admin_analytics_falls_back_gracefully_when_flask_times_out(): void
    {
        // Simulate Flask timeout or connection exception
        Http::fake([
            '*/health' => Http::response(null, 500),
            '*/api/insights*' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection timed out after 5 seconds');
            },
            '*/api/report*' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection timed out');
            },
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/analytics');
        $response->assertStatus(200);
        $response->assertSee('PHP Local Fallback');

        // Test health endpoint on failed connection
        $healthResponse = $this->actingAs($this->admin)->post('/admin/analytics/test-health');
        $healthResponse->assertRedirect();
        $healthResponse->assertSessionHas('error');
    }

    public function test_admin_can_flush_analytics_cache(): void
    {
        Cache::put('some_analytics_key', 'some_value', 60);

        $response = $this->actingAs($this->admin)->post('/admin/analytics/flush-cache');
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_admin_can_generate_python_json_report_via_subprocess(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/analytics/generate-report');
        $response->assertRedirect(route('admin.analytics.view-report'));
        $response->assertSessionHas('success');

        $reportPath = storage_path('app/python_reports/waste_report.json');
        $this->assertFileExists($reportPath);
        
        $json = json_decode(file_get_contents($reportPath), true);
        $this->assertIsArray($json);
        $this->assertArrayHasKey('generated_at', $json);
        $this->assertArrayHasKey('total_searches', $json);
        $this->assertArrayHasKey('category_breakdown', $json);
    }

    public function test_admin_can_view_styled_json_python_report(): void
    {
        $this->actingAs($this->admin)->post('/admin/analytics/generate-report');

        $response = $this->actingAs($this->admin)->get('/admin/analytics/report');
        $response->assertStatus(200);
        $response->assertSee('waste_report.json');
        $response->assertSee('total_searches');
        $response->assertSee('Download JSON Report');
    }

    public function test_admin_can_download_generated_python_json_report(): void
    {
        $this->actingAs($this->admin)->post('/admin/analytics/generate-report');

        $response = $this->actingAs($this->admin)->get('/admin/analytics/download-report');
        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=waste_report.json');
    }

    public function test_missing_report_shows_friendly_message(): void
    {
        $reportPath = storage_path('app/python_reports/waste_report.json');
        if (file_exists($reportPath)) {
            unlink($reportPath);
        }

        $response = $this->actingAs($this->admin)->get('/admin/analytics/report');
        $response->assertStatus(200);
        $response->assertSee('Report Not Available Yet');
        $response->assertSee('Report not available yet. Please generate a report first.');
    }

    public function test_existing_functionality_does_not_regress(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Smart Waste Insights');
    }
}
