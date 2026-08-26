<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\CollectionCentre;
use App\Models\DisposalMethod;
use App\Models\Feedback;
use App\Models\SearchHistory;
use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WasteItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdminUser(): User
    {
        return User::factory()->create([
            'name'     => 'WasteWise Admin',
            'email'    => 'admin@wastewise.in',
            'password' => bcrypt('WasteWise@123'),
            'is_admin' => true,
        ]);
    }

    protected function createNormalUser(): User
    {
        return User::factory()->create([
            'name'     => 'Normal User',
            'email'    => 'user@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => false,
        ]);
    }

    /* ── Phase 1: Categories CRUD ────────────────────────────────────────────── */

    #[Test]
    public function admin_can_create_edit_and_delete_unused_category()
    {
        $admin = $this->createAdminUser();

        // 1. Create category
        $response = $this->actingAs($admin)->post('/admin/categories', [
            'name'        => 'Glass Waste',
            'description' => 'Glass bottles and jars',
        ]);
        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('waste_categories', ['name' => 'Glass Waste']);

        $category = WasteCategory::where('name', 'Glass Waste')->first();

        // 2. Edit category
        $response = $this->actingAs($admin)->put("/admin/categories/{$category->id}", [
            'name'        => 'Glass & Ceramic Waste',
            'description' => 'Updated description',
        ]);
        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('waste_categories', ['name' => 'Glass & Ceramic Waste']);

        // 3. Delete unused category
        $response = $this->actingAs($admin)->delete("/admin/categories/{$category->id}");
        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseMissing('waste_categories', ['id' => $category->id]);
    }

    #[Test]
    public function cannot_delete_category_used_by_waste_item()
    {
        $admin    = $this->createAdminUser();
        $category = WasteCategory::create(['name' => 'E-Waste']);
        $disposal = DisposalMethod::create(['name' => 'Recycle', 'instructions' => 'Drop off at center']);
        
        WasteItem::create([
            'name'               => 'Old Phone',
            'category_id'        => $category->id,
            'disposal_method_id' => $disposal->id,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/categories/{$category->id}");
        $response->assertRedirect('/admin/categories');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('waste_categories', ['id' => $category->id]);
    }

    #[Test]
    public function normal_user_cannot_access_admin_categories()
    {
        $user = $this->createNormalUser();

        $response = $this->actingAs($user)->get('/admin/categories');
        $response->assertStatus(403);
    }

    /* ── Phase 2: Disposal Methods CRUD ──────────────────────────────────────── */

    #[Test]
    public function admin_can_manage_disposal_methods_with_dependency_protection()
    {
        $admin = $this->createAdminUser();

        // Create
        $response = $this->actingAs($admin)->post('/admin/disposal-methods', [
            'name'         => 'Special Incineration',
            'instructions' => 'Take to hazardous waste plant',
            'avoid'        => 'Do not burn at home',
        ]);
        $response->assertRedirect('/admin/disposal-methods');
        $this->assertDatabaseHas('disposal_methods', ['name' => 'Special Incineration']);

        $method = DisposalMethod::where('name', 'Special Incineration')->first();

        // Protect deletion when in use
        $category = WasteCategory::create(['name' => 'Hazardous']);
        WasteItem::create([
            'name'               => 'Paint Can',
            'category_id'        => $category->id,
            'disposal_method_id' => $method->id,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/disposal-methods/{$method->id}");
        $response->assertRedirect('/admin/disposal-methods');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('disposal_methods', ['id' => $method->id]);
    }

    /* ── Phase 3: Cities CRUD ────────────────────────────────────────────────── */

    #[Test]
    public function admin_can_manage_cities_with_dependency_protection()
    {
        $admin = $this->createAdminUser();

        // Create
        $response = $this->actingAs($admin)->post('/admin/cities', [
            'name'  => 'Bengaluru',
            'state' => 'Karnataka',
        ]);
        $response->assertRedirect('/admin/cities');
        $this->assertDatabaseHas('cities', ['name' => 'Bengaluru']);

        $city     = City::where('name', 'Bengaluru')->first();
        $category = WasteCategory::create(['name' => 'Plastic Waste']);

        // Protect deletion when referenced by CollectionCentre
        CollectionCentre::create([
            'name'        => 'City Eco Centre',
            'city_id'     => $city->id,
            'category_id' => $category->id,
            'address'     => '123 Green Way',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/cities/{$city->id}");
        $response->assertRedirect('/admin/cities');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('cities', ['id' => $city->id]);
    }

    /* ── Phase 4: Collection Centres CRUD ────────────────────────────────────── */

    #[Test]
    public function admin_can_create_edit_and_delete_collection_centre()
    {
        $admin    = $this->createAdminUser();
        $city     = City::create(['name' => 'Pune', 'state' => 'Maharashtra']);
        $category = WasteCategory::create(['name' => 'Plastic Waste']);

        $response = $this->actingAs($admin)->post('/admin/collection-centres', [
            'name'        => 'Pune Recycling Hub',
            'city_id'     => $city->id,
            'category_id' => $category->id,
            'address'     => '123 Hadapsar Estate',
            'phone'       => '+91 20 12345678',
            'latitude'    => 18.5204,
            'longitude'   => 73.8567,
        ]);
        $response->assertRedirect('/admin/collection-centres');
        $this->assertDatabaseHas('collection_centres', ['name' => 'Pune Recycling Hub']);

        $centre = CollectionCentre::where('name', 'Pune Recycling Hub')->first();

        // Delete
        $response = $this->actingAs($admin)->delete("/admin/collection-centres/{$centre->id}");
        $response->assertRedirect('/admin/collection-centres');
        $this->assertDatabaseMissing('collection_centres', ['id' => $centre->id]);
    }

    /* ── Phase 5: Waste Items CRUD & API Integration ────────────────────────── */

    #[Test]
    public function newly_created_waste_item_is_immediately_searchable_via_public_api()
    {
        $admin    = $this->createAdminUser();
        $category = WasteCategory::create(['name' => 'Electronic Waste']);
        $disposal = DisposalMethod::create(['name' => 'E-Waste Centre', 'instructions' => 'Drop at authorised center']);

        // Admin adds "Old Printer"
        $response = $this->actingAs($admin)->post('/admin/waste-items', [
            'name'               => 'Old Printer',
            'category_id'        => $category->id,
            'disposal_method_id' => $disposal->id,
            'description'        => 'Used office printer',
            'keywords'           => 'printer, scanner',
        ]);

        $response->assertRedirect('/admin/waste-items');
        $this->assertDatabaseHas('waste_items', ['name' => 'Old Printer']);

        // Public user searches "Old Printer" via API
        $apiResponse = $this->getJson('/api/waste/search?q=Old+Printer');
        $apiResponse->assertStatus(200);
        $apiResponse->assertJsonPath('data.name', 'Old Printer');
        $apiResponse->assertJsonPath('data.category.name', 'Electronic Waste');
        $apiResponse->assertJsonPath('data.disposal_method.name', 'E-Waste Centre');
    }

    /* ── Phase 6: User Management ────────────────────────────────────────────── */

    #[Test]
    public function admin_can_view_and_toggle_user_roles_with_self_protection()
    {
        $admin      = $this->createAdminUser();
        $normalUser = $this->createNormalUser();

        // 1. Admin views user list
        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee($normalUser->email);

        // 2. Admin promotes normal user to admin
        $response = $this->actingAs($admin)->patch("/admin/users/{$normalUser->id}/toggle-admin");
        $response->assertRedirect('/admin/users');
        $this->assertTrue($normalUser->fresh()->isAdmin());

        // 3. Admin CANNOT remove their own admin privileges
        $response = $this->actingAs($admin)->patch("/admin/users/{$admin->id}/toggle-admin");
        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->isAdmin());

        // 4. Admin CANNOT delete their own account
        $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");
        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    /* ── Phase 7: Search History ─────────────────────────────────────────────── */

    #[Test]
    public function admin_can_view_and_delete_search_history()
    {
        $admin    = $this->createAdminUser();
        $user     = $this->createNormalUser();
        $category = WasteCategory::create(['name' => 'Paper']);
        $disposal = DisposalMethod::create(['name' => 'Recycle', 'instructions' => 'Bin it']);
        $item     = WasteItem::create(['name' => 'Newspaper', 'category_id' => $category->id, 'disposal_method_id' => $disposal->id]);

        $history = SearchHistory::create([
            'user_id'       => $user->id,
            'waste_item_id' => $item->id,
            'searched_at'   => now(),
        ]);

        $response = $this->actingAs($admin)->get('/admin/search-history');
        $response->assertStatus(200);
        $response->assertSee('Newspaper');

        $response = $this->actingAs($admin)->delete("/admin/search-history/{$history->id}");
        $response->assertRedirect('/admin/search-history');
        $this->assertDatabaseMissing('search_histories', ['id' => $history->id]);
    }

    /* ── Phase 8: User Feedback Module ───────────────────────────────────────── */

    #[Test]
    public function guest_and_authenticated_users_can_submit_feedback()
    {
        // 1. Guest feedback submission
        $guestResponse = $this->post('/feedback', [
            'name'        => 'Guest Citizen',
            'email'       => 'guest@example.com',
            'rating'      => 5,
            'ease_of_use' => 4,
            'helpful'     => 1,
            'experience'  => 'Great portal for waste disposal information!',
            'suggestion'  => 'Add more collection centres',
            'recommend'   => 1,
        ]);

        $guestResponse->assertRedirect('/feedback');
        $guestResponse->assertSessionHas('success');
        $this->assertDatabaseHas('feedback', [
            'user_id'    => null,
            'name'       => 'Guest Citizen',
            'rating'     => 5,
            'helpful'    => true,
            'recommend'  => true,
        ]);

        // 2. Authenticated user feedback submission
        $user = $this->createNormalUser();
        $userResponse = $this->actingAs($user)->post('/feedback', [
            'rating'      => 4,
            'ease_of_use' => 5,
            'helpful'     => 1,
            'experience'  => 'Very helpful guidance on laptop disposal.',
            'recommend'   => 1,
        ]);

        $userResponse->assertRedirect('/feedback');
        $this->assertDatabaseHas('feedback', [
            'user_id' => $user->id,
            'rating'  => 4,
        ]);
    }

    #[Test]
    public function admin_can_view_show_toggle_reviewed_and_delete_feedback()
    {
        $admin    = $this->createAdminUser();
        $feedback = Feedback::create([
            'name'        => 'Test User',
            'email'       => 'test@example.com',
            'rating'      => 5,
            'ease_of_use' => 5,
            'helpful'     => true,
            'experience'  => 'Awesome experience!',
            'recommend'   => true,
            'is_reviewed' => false,
        ]);

        // Admin views index
        $response = $this->actingAs($admin)->get('/admin/feedback');
        $response->assertStatus(200);

        // Admin views detail
        $response = $this->actingAs($admin)->get("/admin/feedback/{$feedback->id}");
        $response->assertStatus(200);
        $response->assertSee('Awesome experience!');

        // Admin toggles reviewed
        $response = $this->actingAs($admin)->patch("/admin/feedback/{$feedback->id}/toggle-reviewed");
        $this->assertTrue($feedback->fresh()->is_reviewed);

        // Admin deletes feedback
        $response = $this->actingAs($admin)->delete("/admin/feedback/{$feedback->id}");
        $response->assertRedirect('/admin/feedback');
        $this->assertDatabaseMissing('feedback', ['id' => $feedback->id]);
    }
}
