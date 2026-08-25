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

class AuthenticationAndSearchHistoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_view_register_page()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Create Your Account');
    }

    #[Test]
    public function user_can_register_with_valid_details()
    {
        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'testuser@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'testuser@example.com']);
    }

    #[Test]
    public function registration_fails_with_invalid_email()
    {
        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'invalid-email',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function registration_fails_with_short_password()
    {
        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'testuser@example.com',
            'password'              => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    #[Test]
    public function registration_fails_when_password_confirmation_mismatches()
    {
        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'testuser@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    #[Test]
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email'    => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email'    => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'user@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function guest_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function authenticated_user_can_access_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($user->email);
    }

    #[Test]
    public function user_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    #[Test]
    public function authenticated_user_search_records_history()
    {
        $category = WasteCategory::create(['name' => 'Electronic Waste', 'description' => 'E-waste items']);
        $disposal = DisposalMethod::create(['name' => 'Recycle', 'instructions' => 'Take to center']);
        $item     = WasteItem::create([
            'name'               => 'Old Laptop',
            'description'        => 'Used laptop',
            'category_id'        => $category->id,
            'disposal_method_id' => $disposal->id,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/waste/search?q=Old+Laptop');

        $response->assertStatus(200);
        $this->assertDatabaseHas('search_histories', [
            'user_id'       => $user->id,
            'waste_item_id' => $item->id,
        ]);
    }

    #[Test]
    public function failed_search_does_not_create_search_history()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/waste/search?q=xyz99999');

        $response->assertStatus(404);
        $this->assertDatabaseCount('search_histories', 0);
    }

    #[Test]
    public function guest_search_works_without_login_and_does_not_create_search_history()
    {
        $category = WasteCategory::create(['name' => 'Electronic Waste', 'description' => 'E-waste items']);
        $disposal = DisposalMethod::create(['name' => 'Recycle', 'instructions' => 'Take to center']);
        $item     = WasteItem::create([
            'name'               => 'Old Laptop',
            'description'        => 'Used laptop',
            'category_id'        => $category->id,
            'disposal_method_id' => $disposal->id,
        ]);

        $response = $this->getJson('/api/waste/search?q=Old+Laptop');

        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Old Laptop');
        $this->assertDatabaseCount('search_histories', 0);
    }
}
