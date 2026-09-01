<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\KnowledgeArticle;
use App\Models\SustainabilityTip;
use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WasteItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KnowledgeArticleTest extends TestCase
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

    protected function createWasteCategory(string $name = 'Electronic Waste'): WasteCategory
    {
        return WasteCategory::create([
            'name'        => $name,
            'description' => 'Disposed electronics and hardware gadgets.',
        ]);
    }

    #[Test]
    public function guest_can_view_knowledge_hub(): void
    {
        $response = $this->get(route('knowledge.index'));
        $response->assertStatus(200);
        $response->assertSee('Waste Knowledge Hub', false);
    }

    #[Test]
    public function published_article_appears(): void
    {
        $category = $this->createWasteCategory('Electronic Waste');

        $article = KnowledgeArticle::create([
            'title'             => 'How to Dispose of Old Laptops Safely',
            'slug'              => 'how-to-dispose-of-old-laptops-safely',
            'waste_category_id' => $category->id,
            'summary'           => 'Learn laptop e-waste recycling steps.',
            'content'           => 'Wipe battery and hard drive before recycling.',
            'is_published'      => true,
            'published_at'      => now(),
        ]);

        $response = $this->get(route('knowledge.index'));
        $response->assertStatus(200);
        $response->assertSee('How to Dispose of Old Laptops Safely');
        $response->assertSee('Electronic Waste');
    }

    #[Test]
    public function draft_article_does_not_appear(): void
    {
        $category = $this->createWasteCategory('Plastic Waste');

        KnowledgeArticle::create([
            'title'             => 'Draft Plastic Recycling Guide',
            'slug'              => 'draft-plastic-recycling-guide',
            'waste_category_id' => $category->id,
            'content'           => 'Draft content for review.',
            'is_published'      => false,
            'published_at'      => null,
        ]);

        $response = $this->get(route('knowledge.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Draft Plastic Recycling Guide');
    }

    #[Test]
    public function published_article_detail_works(): void
    {
        $category = $this->createWasteCategory('Glass Waste');

        $article = KnowledgeArticle::create([
            'title'             => 'Recycling Glass Bottles Properly',
            'slug'              => 'recycling-glass-bottles-properly',
            'waste_category_id' => $category->id,
            'summary'           => 'Rinse glass jars before bin drop.',
            'content'           => 'Detailed glass bottle sorting and recycling steps.',
            'is_published'      => true,
            'published_at'      => now(),
        ]);

        $response = $this->get(route('knowledge.show', $article->slug));
        $response->assertStatus(200);
        $response->assertSee('Recycling Glass Bottles Properly');
        $response->assertSee('Detailed glass bottle sorting and recycling steps.');
    }

    #[Test]
    public function draft_article_returns_404(): void
    {
        $category = $this->createWasteCategory('Metal Waste');

        $draft = KnowledgeArticle::create([
            'title'             => 'Unpublished Metal Handling Guide',
            'slug'              => 'unpublished-metal-handling-guide',
            'waste_category_id' => $category->id,
            'content'           => 'Unpublished content.',
            'is_published'      => false,
            'published_at'      => null,
        ]);

        $response = $this->get(route('knowledge.show', $draft->slug));
        $response->assertStatus(404);
    }

    #[Test]
    public function admin_can_access_management(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.knowledge-articles.index'));
        $response->assertStatus(200);
        $response->assertSee('Knowledge Articles Management', false);
    }

    #[Test]
    public function admin_can_create_article(): void
    {
        $admin = $this->createAdminUser();
        $category = $this->createWasteCategory('Organic Waste');

        $response = $this->actingAs($admin)->post(route('admin.knowledge-articles.store'), [
            'title'        => 'Composting Kitchen Scraps 101',
            'category'     => $category->id,
            'summary'      => 'Turn food waste into valuable soil fertilizer.',
            'content'      => 'Separate fruit peels and vegetable cuttings into compost bins.',
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('admin.knowledge-articles.index'));

        $this->assertDatabaseHas('knowledge_articles', [
            'title'             => 'Composting Kitchen Scraps 101',
            'slug'              => 'composting-kitchen-scraps-101',
            'waste_category_id' => $category->id,
            'is_published'      => true,
        ]);
    }

    #[Test]
    public function admin_can_edit_article(): void
    {
        $admin = $this->createAdminUser();
        $category = $this->createWasteCategory('Paper Waste');

        $article = KnowledgeArticle::create([
            'title'             => 'Initial Paper Recycling Title',
            'slug'              => 'initial-paper-recycling-title',
            'waste_category_id' => $category->id,
            'content'           => 'Initial content.',
            'is_published'      => true,
            'published_at'      => now(),
        ]);

        $response = $this->actingAs($admin)->put(route('admin.knowledge-articles.update', $article), [
            'title'        => 'Updated Paper Waste Recycling Guide',
            'category'     => $category->id,
            'summary'      => 'Updated summary text.',
            'content'      => 'Updated content text.',
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('admin.knowledge-articles.index'));

        $this->assertDatabaseHas('knowledge_articles', [
            'id'                => $article->id,
            'title'             => 'Updated Paper Waste Recycling Guide',
            'slug'              => 'updated-paper-waste-recycling-guide',
            'waste_category_id' => $category->id,
            'summary'           => 'Updated summary text.',
            'content'           => 'Updated content text.',
        ]);
    }

    #[Test]
    public function admin_can_publish_unpublish(): void
    {
        $admin = $this->createAdminUser();
        $category = $this->createWasteCategory('Hazardous Waste');

        $article = KnowledgeArticle::create([
            'title'             => 'Toggle Article Test',
            'slug'              => 'toggle-article-test',
            'waste_category_id' => $category->id,
            'content'           => 'Content to toggle.',
            'is_published'      => false,
            'published_at'      => null,
        ]);

        // Toggle to Published
        $response = $this->actingAs($admin)->patch(route('admin.knowledge-articles.toggle-published', $article));
        $response->assertRedirect();
        $this->assertDatabaseHas('knowledge_articles', [
            'id'           => $article->id,
            'is_published' => true,
        ]);

        // Toggle back to Unpublished
        $response = $this->actingAs($admin)->patch(route('admin.knowledge-articles.toggle-published', $article));
        $response->assertRedirect();
        $this->assertDatabaseHas('knowledge_articles', [
            'id'           => $article->id,
            'is_published' => false,
        ]);
    }

    #[Test]
    public function admin_can_delete_article(): void
    {
        $admin = $this->createAdminUser();
        $category = $this->createWasteCategory('Textile Waste');

        $article = KnowledgeArticle::create([
            'title'             => 'Article to be Removed',
            'slug'              => 'article-to-be-removed',
            'waste_category_id' => $category->id,
            'content'           => 'Will be deleted.',
            'is_published'      => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.knowledge-articles.destroy', $article));
        $response->assertRedirect(route('admin.knowledge-articles.index'));

        $this->assertDatabaseMissing('knowledge_articles', [
            'id' => $article->id,
        ]);
    }

    #[Test]
    public function normal_user_gets_403(): void
    {
        $user = $this->createNormalUser();

        $response = $this->actingAs($user)->get(route('admin.knowledge-articles.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->get(route('admin.knowledge-articles.create'));
        $response->assertStatus(403);
    }

    #[Test]
    public function validation_works(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.knowledge-articles.store'), [
            'title'    => '',
            'category' => '',
            'content'  => '',
        ]);

        $response->assertSessionHasErrors(['title', 'category', 'content']);
    }

    #[Test]
    public function unique_slug_works(): void
    {
        $admin = $this->createAdminUser();
        $category = $this->createWasteCategory('Electronic Waste');

        $this->actingAs($admin)->post(route('admin.knowledge-articles.store'), [
            'title'    => 'Duplicate Article Title',
            'category' => $category->id,
            'content'  => 'First article content.',
        ]);

        $this->actingAs($admin)->post(route('admin.knowledge-articles.store'), [
            'title'    => 'Duplicate Article Title',
            'category' => $category->id,
            'content'  => 'Second article content.',
        ]);

        $this->assertDatabaseHas('knowledge_articles', ['slug' => 'duplicate-article-title']);
        $this->assertDatabaseHas('knowledge_articles', ['slug' => 'duplicate-article-title-1']);
    }

    #[Test]
    public function category_relationship_works(): void
    {
        $category = $this->createWasteCategory('Plastic Waste');

        $article = KnowledgeArticle::create([
            'title'             => 'Plastic Bottles Handling Guide',
            'slug'              => 'plastic-bottles-handling-guide',
            'waste_category_id' => $category->id,
            'content'           => 'Recycling plastic bottles content.',
            'is_published'      => true,
        ]);

        $this->assertEquals('Plastic Waste', $article->category->name);
        $this->assertTrue($category->knowledgeArticles->contains($article));
    }

    #[Test]
    public function existing_sustainability_tips_still_work(): void
    {
        SustainabilityTip::create([
            'title'        => 'Test Tip Integrity',
            'slug'         => 'test-tip-integrity',
            'category'     => 'Energy',
            'content'      => 'Tip content.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('sustainability-tips.index'));
        $response->assertStatus(200);
        $response->assertSee('Test Tip Integrity');
    }

    #[Test]
    public function existing_announcements_still_work(): void
    {
        Announcement::create([
            'title'        => 'Test Announcement Integrity',
            'slug'         => 'test-announcement-integrity',
            'category'     => 'Update',
            'content'      => 'Announcement content.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('announcements.index'));
        $response->assertStatus(200);
        $response->assertSee('Test Announcement Integrity');
    }

    #[Test]
    public function existing_waste_search_still_works(): void
    {
        $category = $this->createWasteCategory('E-Waste');
        $disposalMethod = \App\Models\DisposalMethod::create([
            'name'         => 'Recycle at E-waste Centre',
            'instructions' => 'Drop off at authorized e-waste collection centres.',
        ]);

        WasteItem::create([
            'name'               => 'Laptop Battery',
            'description'        => 'Rechargeable lithium battery',
            'category_id'        => $category->id,
            'disposal_method_id' => $disposalMethod->id,
        ]);

        $response = $this->get('/api/waste/search?q=Laptop');
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Laptop Battery']);
    }

    #[Test]
    public function navbar_navigation_still_works(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Knowledge Hub', false);

        $response = $this->get(route('knowledge.index'));
        $response->assertStatus(200);
        $response->assertSee('Knowledge Hub', false);
    }
}
