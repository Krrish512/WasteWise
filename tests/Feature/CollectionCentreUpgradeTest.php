<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\CollectionCentre;
use App\Models\User;
use App\Models\WasteCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CollectionCentreUpgradeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;
    protected City $city;
    protected WasteCategory $eWaste;
    protected WasteCategory $plastic;
    protected WasteCategory $metal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->regularUser = User::factory()->create(['is_admin' => false]);

        $this->city = City::create([
            'name'     => 'Surat',
            'state'    => 'Gujarat',
            'district' => 'Surat District',
        ]);

        $this->eWaste = WasteCategory::create(['name' => 'E-Waste', 'description' => 'Electronic Waste']);
        $this->plastic = WasteCategory::create(['name' => 'Plastic', 'description' => 'Plastic Waste']);
        $this->metal = WasteCategory::create(['name' => 'Metal', 'description' => 'Scrap Metal']);
    }

    #[Test]
    public function backward_compatibility_single_category_creation_works_and_populates_pivot(): void
    {
        // Legacy payload only sends category_id without category_ids or new fields
        $response = $this->actingAs($this->admin)->post('/admin/collection-centres', [
            'name'        => 'Legacy Surat Hub',
            'city_id'     => $this->city->id,
            'category_id' => $this->eWaste->id,
            'address'     => 'Ring Road, Surat',
            'phone'       => '9876543210',
            'latitude'    => 21.1702,
            'longitude'   => 72.8311,
        ]);

        $response->assertRedirect('/admin/collection-centres');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('collection_centres', [
            'name'                 => 'Legacy Surat Hub',
            'category_id'          => $this->eWaste->id,
            'facility_type'        => 'public_dropoff',
            'public_access'        => 'public_dropoff',
            'authorization_status' => 'verified',
            'confidence'           => 'medium',
            'status'               => 'active',
        ]);

        $centre = CollectionCentre::where('name', 'Legacy Surat Hub')->first();
        $this->assertNotNull($centre);

        // Verify single-category relationship (backward compatibility)
        $this->assertEquals($this->eWaste->id, $centre->category->id);
        $this->assertEquals('E-Waste', $centre->category->name);

        // Verify pivot table was also populated
        $this->assertDatabaseHas('collection_centre_waste_category', [
            'collection_centre_id' => $centre->id,
            'waste_category_id'    => $this->eWaste->id,
        ]);
        $this->assertTrue($centre->categories->contains($this->eWaste));
    }

    #[Test]
    public function government_recycler_without_gps_coordinates_can_be_staged_safely(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/collection-centres', [
            'name'                      => 'Gujarat State Authorized E-Waste Recycler',
            'city_id'                   => $this->city->id,
            'district'                  => 'Surat District',
            'category_ids'              => [$this->eWaste->id],
            'address'                   => 'GIDC Industrial Estate, Sachin, Surat',
            'phone'                     => '+91 261 2390000',
            'latitude'                  => null,
            'longitude'                 => null,
            'facility_type'             => 'authorized_recycler',
            'public_access'             => 'business_industrial_only',
            'authorization_status'      => 'verified',
            'authorization_id'          => 'GPCB/SURAT/EW/2026/049',
            'authorization_valid_until' => '2028-12-31',
            'website'                   => 'https://gujarat-ewaste.gov.in',
            'source_url'                => 'https://gpcb.gujarat.gov.in/registered-units',
            'verification_date'         => '2026-09-01',
            'confidence'                => 'high',
            'status'                    => 'active',
            'notes'                     => 'Official government registered recycler staged without GPS pin.',
        ]);

        $response->assertRedirect('/admin/collection-centres');

        $this->assertDatabaseHas('collection_centres', [
            'name'                 => 'Gujarat State Authorized E-Waste Recycler',
            'latitude'             => null,
            'longitude'            => null,
            'facility_type'        => 'authorized_recycler',
            'public_access'        => 'business_industrial_only',
            'authorization_status' => 'verified',
            'authorization_id'     => 'GPCB/SURAT/EW/2026/049',
            'confidence'           => 'high',
            'status'               => 'active',
        ]);

        $centre = CollectionCentre::where('name', 'Gujarat State Authorized E-Waste Recycler')->first();
        $this->assertNull($centre->latitude);
        $this->assertNull($centre->longitude);
        $this->assertEquals('GPCB/SURAT/EW/2026/049', $centre->authorization_id);
    }

    #[Test]
    public function multi_category_pivot_table_syncs_and_maintains_primary_foreign_key(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/collection-centres', [
            'name'         => 'Integrated Multi-Stream Recycling Centre',
            'city_id'      => $this->city->id,
            'district'     => 'Surat District',
            'category_ids' => [$this->eWaste->id, $this->plastic->id, $this->metal->id],
            'address'      => 'Plot 44, Hazira Port Road, Surat',
            'phone'        => '9825112233',
            'latitude'     => 21.1000,
            'longitude'    => 72.7000,
            'facility_type'=> 'processing_facility',
        ]);

        $response->assertRedirect('/admin/collection-centres');

        $centre = CollectionCentre::where('name', 'Integrated Multi-Stream Recycling Centre')->first();
        $this->assertNotNull($centre);

        // Primary FK points to first category
        $this->assertEquals($this->eWaste->id, $centre->category_id);

        // Many-to-many relationship contains all 3 categories
        $this->assertCount(3, $centre->categories);
        $this->assertTrue($centre->categories->contains($this->eWaste));
        $this->assertTrue($centre->categories->contains($this->plastic));
        $this->assertTrue($centre->categories->contains($this->metal));

        // Test updating to remove one category and keep two
        $updateResponse = $this->actingAs($this->admin)->put("/admin/collection-centres/{$centre->id}", [
            'name'         => 'Integrated Multi-Stream Recycling Centre',
            'city_id'      => $this->city->id,
            'district'     => 'Surat District',
            'category_ids' => [$this->plastic->id, $this->metal->id],
            'address'      => 'Plot 44, Hazira Port Road, Surat',
            'phone'        => '9825112233',
            'latitude'     => 21.1000,
            'longitude'    => 72.7000,
            'facility_type'=> 'processing_facility',
        ]);

        $updateResponse->assertRedirect('/admin/collection-centres');

        $centre->refresh();
        $this->assertEquals($this->plastic->id, $centre->category_id);
        $this->assertCount(2, $centre->categories);
        $this->assertFalse($centre->categories->contains($this->eWaste));
        $this->assertTrue($centre->categories->contains($this->plastic));
        $this->assertTrue($centre->categories->contains($this->metal));
    }

    #[Test]
    public function all_facility_type_and_public_access_enums_are_supported(): void
    {
        $types = [
            'public_dropoff',
            'municipal_centre',
            'authorized_recycler',
            'processing_facility',
            'pickup_provider',
            'industrial_facility',
        ];

        foreach ($types as $type) {
            $centre = CollectionCentre::create([
                'name'                 => "Centre for {$type}",
                'city_id'              => $this->city->id,
                'category_id'          => $this->plastic->id,
                'address'              => 'Test Address',
                'facility_type'        => $type,
                'public_access'        => 'business_industrial_only',
                'authorization_status' => 'verified',
                'confidence'           => 'high',
                'status'               => 'active',
            ]);

            $this->assertEquals($type, $centre->facility_type);
        }
    }

    #[Test]
    public function admin_can_view_index_and_forms_with_upgraded_fields(): void
    {
        $centre = CollectionCentre::create([
            'name'                      => 'Admin View Centre',
            'city_id'                   => $this->city->id,
            'category_id'               => $this->eWaste->id,
            'district'                  => 'Surat District',
            'address'                   => 'Adajan Road, Surat',
            'facility_type'             => 'municipal_centre',
            'public_access'             => 'public_dropoff',
            'authorization_status'      => 'verified',
            'authorization_id'          => 'SURAT-SMC-001',
            'authorization_valid_until' => '2029-01-01',
            'confidence'                => 'high',
            'status'                    => 'active',
            'notes'                     => 'Primary municipal centre',
        ]);
        $centre->categories()->sync([$this->eWaste->id]);

        // Index page
        $indexResponse = $this->actingAs($this->admin)->get('/admin/collection-centres');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Admin View Centre');
        $indexResponse->assertSee('Surat District');
        $indexResponse->assertSee('municipal centre');

        // Create form
        $createResponse = $this->actingAs($this->admin)->get('/admin/collection-centres/create');
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Facility Classification & Authorization', false);
        $createResponse->assertSee('Geographic Coordinates (Optional)');

        // Edit form
        $editResponse = $this->actingAs($this->admin)->get("/admin/collection-centres/{$centre->id}/edit");
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Admin View Centre');
        $editResponse->assertSee('SURAT-SMC-001');
    }

    #[Test]
    public function city_district_column_can_be_set_and_persisted(): void
    {
        $city = City::create([
            'name'     => 'Bardoli',
            'state'    => 'Gujarat',
            'district' => 'Surat District',
        ]);

        $this->assertEquals('Surat District', $city->district);
        $this->assertDatabaseHas('cities', [
            'name'     => 'Bardoli',
            'district' => 'Surat District',
        ]);
    }

    #[Test]
    public function non_admin_cannot_access_collection_centres_crud(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/admin/collection-centres');
        $response->assertStatus(403);

        $postResponse = $this->actingAs($this->regularUser)->post('/admin/collection-centres', [
            'name'        => 'Unauthorized Hub',
            'city_id'     => $this->city->id,
            'category_id' => $this->eWaste->id,
            'address'     => 'Nowhere',
        ]);
        $postResponse->assertStatus(403);
    }
}
