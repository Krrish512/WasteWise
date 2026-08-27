<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\CollectionCentre;
use App\Models\User;
use App\Models\WasteCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CollectionCentreNearestTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function public_home_page_loads_and_passes_collection_centres_with_coordinates()
    {
        $city = City::create(['name' => 'Mumbai', 'state' => 'Maharashtra']);
        $category = WasteCategory::create(['name' => 'Electronic Waste']);

        $centre = CollectionCentre::create([
            'name'        => 'Mumbai E-Waste Hub',
            'city_id'     => $city->id,
            'category_id' => $category->id,
            'address'     => 'Andheri East, Mumbai',
            'phone'       => '9876543210',
            'latitude'    => 19.1136,
            'longitude'   => 72.8697,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Find Nearest Centre');
        $response->assertSee('Show All Centres');
        $response->assertSee('Mumbai E-Waste Hub');
    }

    #[Test]
    public function centre_with_null_coordinates_loads_safely_without_errors()
    {
        $city = City::create(['name' => 'Delhi', 'state' => 'Delhi']);
        $category = WasteCategory::create(['name' => 'Plastic Waste']);

        CollectionCentre::create([
            'name'        => 'Delhi Plastic Hub',
            'city_id'     => $city->id,
            'category_id' => $category->id,
            'address'     => 'Okhla, Delhi',
            'phone'       => '9123456789',
            'latitude'    => null,
            'longitude'   => null,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Delhi Plastic Hub');
    }

    #[Test]
    public function admin_created_collection_centre_immediately_appears_in_public_data()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $city = City::create(['name' => 'Pune', 'state' => 'Maharashtra']);
        $category = WasteCategory::create(['name' => 'Paper Waste']);

        $response = $this->actingAs($admin)->post('/admin/collection-centres', [
            'name'        => 'Pune Paper Recycling',
            'city_id'     => $city->id,
            'category_id' => $category->id,
            'address'     => 'Hadapsar, Pune',
            'phone'       => '9988776655',
            'latitude'    => 18.5204,
            'longitude'   => 73.8567,
        ]);

        $response->assertRedirect('/admin/collection-centres');

        // Check public view
        $publicResponse = $this->get('/');
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee('Pune Paper Recycling');
    }
}
