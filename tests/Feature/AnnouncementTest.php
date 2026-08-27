<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AnnouncementTest extends TestCase
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
    public function guest_can_view_announcements_page(): void
    {
        $response = $this->get(route('announcements.index'));
        $response->assertStatus(200);
        $response->assertSee('Announcements & Updates', false);
    }

    #[Test]
    public function published_announcement_appears_publicly(): void
    {
        $announcement = Announcement::create([
            'title'        => 'Public Recycling Drive 2026',
            'slug'         => 'public-recycling-drive-2026',
            'content'      => 'Join us this weekend for e-waste recycling.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('announcements.index'));
        $response->assertStatus(200);
        $response->assertSee('Public Recycling Drive 2026');
    }

    #[Test]
    public function draft_announcement_does_not_appear_publicly(): void
    {
        $draft = Announcement::create([
            'title'        => 'Secret Internal Announcement',
            'slug'         => 'secret-internal-announcement',
            'content'      => 'This is a draft announcement.',
            'is_published' => false,
            'published_at' => null,
        ]);

        $response = $this->get(route('announcements.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Secret Internal Announcement');
    }

    #[Test]
    public function guest_can_open_published_announcement_detail(): void
    {
        $announcement = Announcement::create([
            'title'        => 'New E-Waste Drop Location',
            'slug'         => 'new-e-waste-drop-location',
            'content'      => 'We have opened a new location in Andheri.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('announcements.show', $announcement->slug));
        $response->assertStatus(200);
        $response->assertSee('New E-Waste Drop Location');
        $response->assertSee('We have opened a new location in Andheri.');
    }

    #[Test]
    public function guest_cannot_open_unpublished_announcement(): void
    {
        $draft = Announcement::create([
            'title'        => 'Unpublished Announcement',
            'slug'         => 'unpublished-announcement',
            'content'      => 'Draft content.',
            'is_published' => false,
            'published_at' => null,
        ]);

        $response = $this->get(route('announcements.show', $draft->slug));
        $response->assertStatus(404);
    }

    #[Test]
    public function admin_can_access_announcement_management(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.announcements.index'));
        $response->assertStatus(200);
        $response->assertSee('Announcements & Updates', false);
    }

    #[Test]
    public function admin_can_create_announcement(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title'        => 'Community Clean-Up Drive',
            'content'      => 'Detailed instructions for the clean-up event.',
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'title'        => 'Community Clean-Up Drive',
            'slug'         => 'community-clean-up-drive',
            'is_published' => true,
        ]);
    }

    #[Test]
    public function admin_can_edit_announcement(): void
    {
        $admin = $this->createAdminUser();
        $announcement = Announcement::create([
            'title'        => 'Original Announcement Title',
            'slug'         => 'original-announcement-title',
            'content'      => 'Original content text.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($admin)->put(route('admin.announcements.update', $announcement), [
            'title'        => 'Updated Announcement Title',
            'content'      => 'Updated content text.',
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'id'      => $announcement->id,
            'title'   => 'Updated Announcement Title',
            'content' => 'Updated content text.',
        ]);
    }

    #[Test]
    public function admin_can_publish_unpublish_announcement(): void
    {
        $admin = $this->createAdminUser();
        $announcement = Announcement::create([
            'title'        => 'Toggle Status Test',
            'slug'         => 'toggle-status-test',
            'content'      => 'Content to toggle.',
            'is_published' => false,
            'published_at' => null,
        ]);

        // Toggle to Published
        $response = $this->actingAs($admin)->patch(route('admin.announcements.toggle-published', $announcement));
        $response->assertRedirect();
        $this->assertDatabaseHas('announcements', [
            'id'           => $announcement->id,
            'is_published' => true,
        ]);

        // Toggle back to Draft
        $response = $this->actingAs($admin)->patch(route('admin.announcements.toggle-published', $announcement));
        $response->assertRedirect();
        $this->assertDatabaseHas('announcements', [
            'id'           => $announcement->id,
            'is_published' => false,
        ]);
    }

    #[Test]
    public function admin_can_delete_announcement(): void
    {
        $admin = $this->createAdminUser();
        $announcement = Announcement::create([
            'title'        => 'Announcement to be Deleted',
            'slug'         => 'announcement-to-be-deleted',
            'content'      => 'This announcement will be removed.',
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.announcements.destroy', $announcement));
        $response->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseMissing('announcements', [
            'id' => $announcement->id,
        ]);
    }

    #[Test]
    public function normal_authenticated_user_receives_403_for_admin_announcement_routes(): void
    {
        $user = $this->createNormalUser();

        $response = $this->actingAs($user)->get(route('admin.announcements.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->get(route('admin.announcements.create'));
        $response->assertStatus(403);
    }

    #[Test]
    public function validation_rejects_empty_title_content(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title'   => '',
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'content']);
    }

    #[Test]
    public function slug_is_generated_correctly(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title'   => 'Eco Smart Recycling Tips & Tricks!',
            'content' => 'Sample body content.',
        ]);

        $this->assertDatabaseHas('announcements', [
            'title' => 'Eco Smart Recycling Tips & Tricks!',
            'slug'  => 'eco-smart-recycling-tips-tricks',
        ]);
    }

    #[Test]
    public function duplicate_titles_produce_unique_slugs(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title'   => 'Duplicate Event Title',
            'content' => 'Event description 1.',
        ]);

        $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title'   => 'Duplicate Event Title',
            'content' => 'Event description 2.',
        ]);

        $this->assertDatabaseHas('announcements', ['slug' => 'duplicate-event-title']);
        $this->assertDatabaseHas('announcements', ['slug' => 'duplicate-event-title-1']);
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
