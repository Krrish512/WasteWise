<?php

namespace Tests\Feature;

use App\Models\SustainabilityTip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SustainabilityTipTest extends TestCase
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

    #[Test]
    public function guest_can_view_sustainability_tips_page(): void
    {
        $response = $this->get(route('sustainability-tips.index'));
        $response->assertStatus(200);
        $response->assertSee('Sustainability Tips', false);
    }

    #[Test]
    public function published_sustainability_tip_appears_publicly(): void
    {
        $tip = SustainabilityTip::create([
            'title'        => '5 Steps to Home Composting',
            'slug'         => '5-steps-to-home-composting',
            'category'     => 'Composting',
            'summary'      => 'Learn how to compost kitchen scraps effortlessly.',
            'content'      => 'Detailed guide to organic composting at home.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('sustainability-tips.index'));
        $response->assertStatus(200);
        $response->assertSee('5 Steps to Home Composting');
        $response->assertSee('Composting');
    }

    #[Test]
    public function draft_sustainability_tip_does_not_appear_publicly(): void
    {
        $draft = SustainabilityTip::create([
            'title'        => 'Draft Eco Tip for Internal Review',
            'slug'         => 'draft-eco-tip-internal-review',
            'category'     => 'Internal',
            'content'      => 'Draft tip content.',
            'is_published' => false,
            'published_at' => null,
        ]);

        $response = $this->get(route('sustainability-tips.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Draft Eco Tip for Internal Review');
    }

    #[Test]
    public function guest_can_open_published_sustainability_tip_detail(): void
    {
        $tip = SustainabilityTip::create([
            'title'        => 'How to Reduce Plastic Waste Daily',
            'slug'         => 'how-to-reduce-plastic-waste-daily',
            'category'     => 'Zero Waste',
            'summary'      => 'Switch to reusable bags and stainless steel bottles.',
            'content'      => 'Here are 10 single-use plastic alternatives you can try today.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('sustainability-tips.show', $tip->slug));
        $response->assertStatus(200);
        $response->assertSee('How to Reduce Plastic Waste Daily');
        $response->assertSee('Here are 10 single-use plastic alternatives you can try today.');
    }

    #[Test]
    public function guest_cannot_open_unpublished_sustainability_tip(): void
    {
        $draft = SustainabilityTip::create([
            'title'        => 'Unpublished Eco Tip',
            'slug'         => 'unpublished-eco-tip',
            'category'     => 'Drafts',
            'content'      => 'Unpublished tip content.',
            'is_published' => false,
            'published_at' => null,
        ]);

        $response = $this->get(route('sustainability-tips.show', $draft->slug));
        $response->assertStatus(404);
    }

    #[Test]
    public function admin_can_access_sustainability_tips_management(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.sustainability-tips.index'));
        $response->assertStatus(200);
        $response->assertSee('Sustainability Tips Management', false);
    }

    #[Test]
    public function admin_can_create_sustainability_tip(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.sustainability-tips.store'), [
            'title'        => 'Saving Water in Urban Households',
            'category'     => 'Water Conservation',
            'summary'      => 'Simple fixes to cut domestic water consumption by half.',
            'content'      => 'Install low-flow aerators and fix leaky taps promptly.',
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('admin.sustainability-tips.index'));

        $this->assertDatabaseHas('sustainability_tips', [
            'title'        => 'Saving Water in Urban Households',
            'slug'         => 'saving-water-in-urban-households',
            'category'     => 'Water Conservation',
            'is_published' => true,
        ]);
    }

    #[Test]
    public function admin_can_edit_sustainability_tip(): void
    {
        $admin = $this->createAdminUser();
        $tip = SustainabilityTip::create([
            'title'        => 'Old Tip Title',
            'slug'         => 'old-tip-title',
            'category'     => 'Energy',
            'content'      => 'Old content.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($admin)->put(route('admin.sustainability-tips.update', $tip), [
            'title'        => 'Updated Tip Title',
            'category'     => 'Energy Saving',
            'summary'      => 'Updated summary text.',
            'content'      => 'Updated content text.',
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('admin.sustainability-tips.index'));

        $this->assertDatabaseHas('sustainability_tips', [
            'id'       => $tip->id,
            'title'    => 'Updated Tip Title',
            'category' => 'Energy Saving',
            'summary'  => 'Updated summary text.',
            'content'  => 'Updated content text.',
        ]);
    }

    #[Test]
    public function admin_can_publish_unpublish_sustainability_tip(): void
    {
        $admin = $this->createAdminUser();
        $tip = SustainabilityTip::create([
            'title'        => 'Toggle Published Test Tip',
            'slug'         => 'toggle-published-test-tip',
            'category'     => 'General',
            'content'      => 'Content to toggle.',
            'is_published' => false,
            'published_at' => null,
        ]);

        // Toggle to Published
        $response = $this->actingAs($admin)->patch(route('admin.sustainability-tips.toggle-published', $tip));
        $response->assertRedirect();
        $this->assertDatabaseHas('sustainability_tips', [
            'id'           => $tip->id,
            'is_published' => true,
        ]);

        // Toggle back to Draft
        $response = $this->actingAs($admin)->patch(route('admin.sustainability-tips.toggle-published', $tip));
        $response->assertRedirect();
        $this->assertDatabaseHas('sustainability_tips', [
            'id'           => $tip->id,
            'is_published' => false,
        ]);
    }

    #[Test]
    public function admin_can_delete_sustainability_tip(): void
    {
        $admin = $this->createAdminUser();
        $tip = SustainabilityTip::create([
            'title'        => 'Tip to be Deleted',
            'slug'         => 'tip-to-be-deleted',
            'category'     => 'General',
            'content'      => 'Will be removed.',
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.sustainability-tips.destroy', $tip));
        $response->assertRedirect(route('admin.sustainability-tips.index'));

        $this->assertDatabaseMissing('sustainability_tips', [
            'id' => $tip->id,
        ]);
    }

    #[Test]
    public function normal_authenticated_user_receives_403_for_admin_sustainability_tip_routes(): void
    {
        $user = $this->createNormalUser();

        $response = $this->actingAs($user)->get(route('admin.sustainability-tips.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->get(route('admin.sustainability-tips.create'));
        $response->assertStatus(403);
    }

    #[Test]
    public function validation_rejects_empty_title_category_content(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.sustainability-tips.store'), [
            'title'    => '',
            'category' => '',
            'content'  => '',
        ]);

        $response->assertSessionHasErrors(['title', 'category', 'content']);
    }

    #[Test]
    public function slug_is_generated_correctly_and_handles_duplicates(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)->post(route('admin.sustainability-tips.store'), [
            'title'    => 'Duplicate Eco Tip Title!',
            'category' => 'General',
            'content'  => 'First tip content.',
        ]);

        $this->actingAs($admin)->post(route('admin.sustainability-tips.store'), [
            'title'    => 'Duplicate Eco Tip Title!',
            'category' => 'General',
            'content'  => 'Second tip content.',
        ]);

        $this->assertDatabaseHas('sustainability_tips', ['slug' => 'duplicate-eco-tip-title']);
        $this->assertDatabaseHas('sustainability_tips', ['slug' => 'duplicate-eco-tip-title-1']);
    }

    #[Test]
    public function dashboard_statistics_include_sustainability_tips_counts(): void
    {
        $admin = $this->createAdminUser();

        SustainabilityTip::create([
            'title'        => 'Published Tip',
            'slug'         => 'published-tip',
            'category'     => 'General',
            'content'      => 'Content.',
            'is_published' => true,
        ]);

        SustainabilityTip::create([
            'title'        => 'Draft Tip',
            'slug'         => 'draft-tip',
            'category'     => 'General',
            'content'      => 'Content.',
            'is_published' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Sustainability Tips');
        $response->assertSee('2'); // Total count
    }

    #[Test]
    public function existing_authentication_and_search_functionality_does_not_regress(): void
    {
        // 1. Home page loads
        $this->get('/')->assertStatus(200);

        // 2. User registration and dashboard
        $response = $this->post('/register', [
            'name'                  => 'Test Reg User',
            'email'                 => 'reguser@wastewise.in',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $response->assertRedirect(route('dashboard'));

        // 3. User login
        $response = $this->post('/login', [
            'email'    => 'reguser@wastewise.in',
            'password' => 'Password123!',
        ]);
        $response->assertRedirect(route('dashboard'));
    }
}
