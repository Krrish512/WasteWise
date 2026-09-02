<?php

namespace Tests\Feature;

use App\Models\SearchHistory;
use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WasteItem;
use App\Models\DisposalMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTrendsApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * GET /api/search-trends returns valid JSON structure.
     */
    public function test_search_trends_returns_json_structure(): void
    {
        $response = $this->getJson('/api/search-trends');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'total_searches',
                'focus',
                'records',
            ])
            ->assertJson([
                'success' => true,
                'focus'   => 'all',
            ]);
    }

    /**
     * Empty database returns total_searches = 0 gracefully.
     */
    public function test_search_trends_empty_database(): void
    {
        $response = $this->getJson('/api/search-trends');

        $response->assertStatus(200)
            ->assertJson([
                'success'        => true,
                'total_searches' => 0,
                'records'        => [],
            ]);
    }

    /**
     * Returns real search history records with category and item names.
     */
    public function test_search_trends_returns_real_records(): void
    {
        $category = WasteCategory::create(['name' => 'Electronic Waste']);
        $disposal = DisposalMethod::create([
            'name'         => 'E-Waste Recycling',
            'instructions' => 'Take to certified e-waste recycler.',
            'avoid'        => 'Do not throw in regular trash.',
        ]);
        $item = WasteItem::create([
            'name'              => 'Old Laptop',
            'category_id'       => $category->id,
            'disposal_method_id' => $disposal->id,
        ]);
        $user = User::factory()->create();

        SearchHistory::create([
            'user_id'       => $user->id,
            'waste_item_id' => $item->id,
            'searched_at'   => now(),
        ]);

        $response = $this->getJson('/api/search-trends');

        $response->assertStatus(200)
            ->assertJson([
                'success'        => true,
                'total_searches' => 1,
            ]);

        $records = $response->json('records');
        $this->assertCount(1, $records);
        $this->assertEquals('Old Laptop', $records[0]['waste_item']);
        $this->assertEquals('Electronic Waste', $records[0]['category']);
    }

    /**
     * Focus filtering returns only matching category records.
     */
    public function test_search_trends_focus_filters_correctly(): void
    {
        $eCat = WasteCategory::create(['name' => 'Electronic Waste']);
        $pCat = WasteCategory::create(['name' => 'Plastic Waste']);
        $disposal = DisposalMethod::create([
            'name'         => 'Recycling',
            'instructions' => 'Recycle properly.',
            'avoid'        => 'Do not mix.',
        ]);
        $eItem = WasteItem::create([
            'name'              => 'Old Laptop',
            'category_id'       => $eCat->id,
            'disposal_method_id' => $disposal->id,
        ]);
        $pItem = WasteItem::create([
            'name'              => 'Plastic Bottle',
            'category_id'       => $pCat->id,
            'disposal_method_id' => $disposal->id,
        ]);
        $user = User::factory()->create();

        SearchHistory::create([
            'user_id'       => $user->id,
            'waste_item_id' => $eItem->id,
            'searched_at'   => now(),
        ]);
        SearchHistory::create([
            'user_id'       => $user->id,
            'waste_item_id' => $pItem->id,
            'searched_at'   => now(),
        ]);
        SearchHistory::create([
            'user_id'       => $user->id,
            'waste_item_id' => $pItem->id,
            'searched_at'   => now(),
        ]);

        // All: 3 records
        $allResponse = $this->getJson('/api/search-trends?focus=all');
        $allResponse->assertJson(['total_searches' => 3]);

        // Electronic: 1 record
        $eResponse = $this->getJson('/api/search-trends?focus=electronic');
        $eResponse->assertJson(['total_searches' => 1, 'focus' => 'electronic']);
        $eRecords = $eResponse->json('records');
        $this->assertCount(1, $eRecords);
        $this->assertEquals('Electronic Waste', $eRecords[0]['category']);

        // Plastic: 2 records
        $pResponse = $this->getJson('/api/search-trends?focus=plastic');
        $pResponse->assertJson(['total_searches' => 2, 'focus' => 'plastic']);
    }

    /**
     * Unknown focus value defaults to returning all records.
     */
    public function test_search_trends_unknown_focus_returns_all(): void
    {
        $response = $this->getJson('/api/search-trends?focus=nonexistent');

        $response->assertStatus(200)
            ->assertJson([
                'success'        => true,
                'total_searches' => 0,
            ]);
    }

    /**
     * Existing waste search API still works after adding search-trends.
     */
    public function test_existing_waste_search_api_not_broken(): void
    {
        $response = $this->getJson('/api/waste/search?q=TestItem');

        // Should return 404 (item not found) but NOT 500
        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }
}
