<?php

namespace Tests\Feature;

use App\Models\SearchHistory;
use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WasteItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_admin_can_generate_python_report_via_subprocess(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/analytics/generate-report');
        $response->assertRedirect(route('admin.analytics.view-report'));
        $response->assertSessionHas('success');

        $reportPath = storage_path('app/python_reports/waste_report.txt');
        $this->assertFileExists($reportPath);
    }

    public function test_admin_can_view_styled_html_python_report(): void
    {
        // First generate
        $this->actingAs($this->admin)->post('/admin/analytics/generate-report');

        $response = $this->actingAs($this->admin)->get('/admin/analytics/report');
        $response->assertStatus(200);
        $response->assertSee('Executive Analytics Report');
        $response->assertSee('Total Searches');
        $response->assertSee('Download Report');
    }

    public function test_admin_can_download_generated_python_report(): void
    {
        // First generate
        $this->actingAs($this->admin)->post('/admin/analytics/generate-report');

        $response = $this->actingAs($this->admin)->get('/admin/analytics/download-report');
        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=WasteWise_Analytics_Report.txt');
    }

    public function test_missing_report_shows_friendly_message(): void
    {
        $reportPath = storage_path('app/python_reports/waste_report.txt');
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
