<?php

namespace Tests\Feature;

use App\Models\DisposalMethod;
use App\Models\SearchHistory;
use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WasteItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WasteSearchTest extends TestCase
{
    use RefreshDatabase;

    protected WasteCategory $category;
    protected DisposalMethod $disposal;
    protected WasteItem $mobile;
    protected WasteItem $laptop;
    protected WasteItem $bottle;
    protected WasteItem $newspaper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = WasteCategory::create([
            'name' => 'Electronic Waste',
            'description' => 'E-waste items'
        ]);

        $this->disposal = DisposalMethod::create([
            'name' => 'E-Waste Recycling Centre',
            'instructions' => 'Take to authorized centre'
        ]);

        $this->mobile = WasteItem::create([
            'name'               => 'Old Mobile',
            'description'        => 'Used mobile phone',
            'keywords'           => 'mobile, phone, smartphone, cell phone, mobile phone, cellphone',
            'category_id'        => $this->category->id,
            'disposal_method_id' => $this->disposal->id,
        ]);

        $this->laptop = WasteItem::create([
            'name'               => 'Old Laptop',
            'description'        => 'Used laptop',
            'keywords'           => 'laptop, notebook, computer, pc, broken laptop',
            'category_id'        => $this->category->id,
            'disposal_method_id' => $this->disposal->id,
        ]);

        $this->bottle = WasteItem::create([
            'name'               => 'Plastic Bottle',
            'description'        => 'PET plastic bottle',
            'keywords'           => 'plastic, bottle, water bottle, plastic bottles',
            'category_id'        => $this->category->id,
            'disposal_method_id' => $this->disposal->id,
        ]);

        $this->newspaper = WasteItem::create([
            'name'               => 'Newspaper',
            'description'        => 'Old newspapers',
            'keywords'           => 'newspaper, paper, news, newspapers',
            'category_id'        => $this->category->id,
            'disposal_method_id' => $this->disposal->id,
        ]);
    }

    /** @test */
    public function exact_waste_item_name_match()
    {
        $response = $this->getJson('/api/waste/search?q=Plastic+Bottle');

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Plastic Bottle');
    }

    /** @test */
    public function case_insensitive_match()
    {
        $response = $this->getJson('/api/waste/search?q=newspaper');

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Newspaper');
    }

    /** @test */
    public function keyword_based_match_mobile_phone_to_old_mobile()
    {
        $response = $this->getJson('/api/waste/search?q=mobile+phone');

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Old Mobile');
    }

    /** @test */
    public function keyword_based_match_smartphone_to_old_mobile()
    {
        $response = $this->getJson('/api/waste/search?q=smartphone');

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Old Mobile');
    }

    /** @test */
    public function token_match_broken_laptop_to_old_laptop()
    {
        $response = $this->getJson('/api/waste/search?q=broken+laptop');

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Old Laptop');
    }

    /** @test */
    public function plural_normalization_plastic_bottles_to_plastic_bottle()
    {
        $response = $this->getJson('/api/waste/search?q=plastic+bottles');

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Plastic Bottle');
    }

    /** @test */
    public function unknown_item_returns_404_with_suggestions_and_admin_note()
    {
        $response = $this->getJson('/api/waste/search?q=xyz99999');

        $response->assertStatus(404)
            ->assertJson(['success' => false])
            ->assertJsonStructure(['message', 'suggestions', 'admin_note']);
    }

    /** @test */
    public function authenticated_user_search_records_history()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/waste/search?q=mobile+phone');

        $response->assertStatus(200);
        $this->assertDatabaseHas('search_histories', [
            'user_id'       => $user->id,
            'waste_item_id' => $this->mobile->id,
        ]);
    }

    /** @test */
    public function failed_search_does_not_create_search_history()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/waste/search?q=unknownitem123');

        $response->assertStatus(404);
        $this->assertDatabaseCount('search_histories', 0);
    }

    /** @test */
    public function guest_search_works_without_login_and_does_not_create_search_history()
    {
        $response = $this->getJson('/api/waste/search?q=Old+Laptop');

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Old Laptop');
        $this->assertDatabaseCount('search_histories', 0);
    }
}
