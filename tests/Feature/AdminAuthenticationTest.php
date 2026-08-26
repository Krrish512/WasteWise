<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WasteItem;
use App\Models\WasteCategory;
use App\Models\DisposalMethod;
use App\Models\SearchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdminUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'name'     => 'WasteWise Admin',
            'email'    => 'admin@wastewise.in',
            'password' => bcrypt('WasteWise@123'),
            'is_admin' => true,
        ], $overrides));
    }

    protected function createNormalUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'name'     => 'Normal User',
            'email'    => 'user@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => false,
        ], $overrides));
    }

    /* ── A. Admin Login Tests ────────────────────────────────────────────────── */

    #[Test]
    public function valid_admin_credentials_login_succeeds_and_redirects_to_admin_dashboard()
    {
        $admin = $this->createAdminUser();

        $response = $this->post('/admin/login', [
            'email'    => 'admin@wastewise.in',
            'password' => 'WasteWise@123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    #[Test]
    public function invalid_admin_password_login_fails()
    {
        $this->createAdminUser();

        $response = $this->post('/admin/login', [
            'email'    => 'admin@wastewise.in',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function invalid_email_login_fails()
    {
        $this->createAdminUser();

        $response = $this->post('/admin/login', [
            'email'    => 'nonexistent@wastewise.in',
            'password' => 'WasteWise@123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /* ── B. Admin Authorization Tests ────────────────────────────────────────── */

    #[Test]
    public function guest_accessing_admin_dashboard_redirects_to_admin_login()
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/admin/login');
    }

    #[Test]
    public function normal_authenticated_user_accessing_admin_dashboard_returns_403()
    {
        $user = $this->createNormalUser();

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    #[Test]
    public function normal_user_attempting_admin_login_is_rejected_and_logged_out()
    {
        $user = $this->createNormalUser();

        $response = $this->post('/admin/login', [
            'email'    => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function admin_can_access_admin_dashboard()
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('WasteWise Management Console');
    }

    /* ── C. Admin Logout Tests ───────────────────────────────────────────────── */

    #[Test]
    public function admin_logout_invalidates_session_and_redirects_to_admin_login()
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post('/admin/logout');

        $response->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    #[Test]
    public function after_logout_admin_dashboard_is_inaccessible()
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)->post('/admin/logout');

        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    /* ── D. Normal User Regression Tests ─────────────────────────────────────── */

    #[Test]
    public function normal_user_login_still_works()
    {
        $user = $this->createNormalUser();

        $response = $this->post('/login', [
            'email'    => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function user_dashboard_still_works_for_normal_users()
    {
        $user = $this->createNormalUser();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Welcome back, Normal User!');
    }

    #[Test]
    public function public_old_laptop_search_still_works()
    {
        $category = WasteCategory::create(['name' => 'Electronic Waste', 'description' => 'E-waste items']);
        $disposal = DisposalMethod::create(['name' => 'Recycle', 'instructions' => 'Take to center']);
        WasteItem::create([
            'name'               => 'Old Laptop',
            'description'        => 'Used laptop',
            'category_id'        => $category->id,
            'disposal_method_id' => $disposal->id,
        ]);

        $response = $this->getJson('/api/waste/search?q=Old+Laptop');

        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Old Laptop');
    }

    #[Test]
    public function authenticated_normal_user_search_still_works_and_records_history()
    {
        $category = WasteCategory::create(['name' => 'Electronic Waste', 'description' => 'E-waste items']);
        $disposal = DisposalMethod::create(['name' => 'Recycle', 'instructions' => 'Take to center']);
        $item     = WasteItem::create([
            'name'               => 'Old Laptop',
            'description'        => 'Used laptop',
            'category_id'        => $category->id,
            'disposal_method_id' => $disposal->id,
        ]);

        $user = $this->createNormalUser();

        $response = $this->actingAs($user)->getJson('/api/waste/search?q=Old+Laptop');

        $response->assertStatus(200);
        $this->assertDatabaseHas('search_histories', [
            'user_id'       => $user->id,
            'waste_item_id' => $item->id,
        ]);
    }

    /* ── E. Admin Regression Tests ───────────────────────────────────────────── */

    #[Test]
    public function admin_cannot_accidentally_lose_normal_public_search_access()
    {
        $category = WasteCategory::create(['name' => 'Electronic Waste', 'description' => 'E-waste items']);
        $disposal = DisposalMethod::create(['name' => 'Recycle', 'instructions' => 'Take to center']);
        WasteItem::create([
            'name'               => 'Old Laptop',
            'description'        => 'Used laptop',
            'category_id'        => $category->id,
            'disposal_method_id' => $disposal->id,
        ]);

        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->getJson('/api/waste/search?q=Old+Laptop');

        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Old Laptop');
    }
}
