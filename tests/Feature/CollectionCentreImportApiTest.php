<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\CollectionCentre;
use App\Models\User;
use App\Models\WasteCategory;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionCentreImportApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $normalUser;
    protected City $citySurat;
    protected City $cityAhmedabad;
    protected WasteCategory $categoryEWaste;
    protected WasteCategory $categoryPlastic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->normalUser = User::factory()->create(['is_admin' => false]);

        $this->citySurat = City::create(['name' => 'Surat', 'state' => 'Gujarat', 'district' => 'Surat District']);
        $this->cityAhmedabad = City::create(['name' => 'Ahmedabad', 'state' => 'Gujarat', 'district' => 'Ahmedabad District']);

        $this->categoryEWaste = WasteCategory::create(['name' => 'Electronic Waste', 'description' => 'E-waste items']);
        $this->categoryPlastic = WasteCategory::create(['name' => 'Plastic Waste', 'description' => 'Plastic items']);
    }

    /**
     * Unauthenticated request is blocked with 401.
     */
    public function test_unauthenticated_request_blocked(): void
    {
        $response = $this->postJson('/api/admin/collection-centres/import', [
            'centres' => [
                [
                    'name'     => 'Test Hub',
                    'category' => 'Electronic Waste',
                    'city'     => 'Surat',
                    'address'  => 'Ring Road',
                ]
            ]
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * Non-admin authenticated user is blocked with 403.
     */
    public function test_non_admin_user_blocked(): void
    {
        $response = $this->actingAs($this->normalUser)
            ->postJson('/api/admin/collection-centres/import', [
                'centres' => [
                    [
                        'name'     => 'Test Hub',
                        'category' => 'Electronic Waste',
                        'city'     => 'Surat',
                        'address'  => 'Ring Road',
                    ]
                ]
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Admin user can successfully bulk import multiple valid collection centres.
     */
    public function test_admin_successful_bulk_import(): void
    {
        $payload = [
            'centres' => [
                [
                    'name'      => 'Surat E-Waste Hub',
                    'category'  => 'Electronic Waste',
                    'city'      => 'Surat',
                    'address'   => 'Ring Road Near Station',
                    'latitude'  => 21.170243,
                    'longitude' => 72.831100,
                    'phone'     => '9876543210',
                ],
                [
                    'name'      => 'Ahmedabad Plastic Depot',
                    'category'  => 'Plastic Waste',
                    'city'      => 'Ahmedabad',
                    'address'   => 'CG Road Navrangpura',
                    'latitude'  => 23.022505,
                    'longitude' => 72.571362,
                    'phone'     => '9876543211',
                ],
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/collection-centres/import', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success'            => true,
                'imported'           => 2,
                'inserted'           => 2,
                'skipped_duplicates' => 0,
                'failed'             => 0,
            ]);

        $this->assertDatabaseHas('collection_centres', [
            'name'        => 'Surat E-Waste Hub',
            'city_id'     => $this->citySurat->id,
            'category_id' => $this->categoryEWaste->id,
            'address'     => 'Ring Road Near Station',
        ]);

        $this->assertDatabaseHas('collection_centres', [
            'name'        => 'Ahmedabad Plastic Depot',
            'city_id'     => $this->cityAhmedabad->id,
            'category_id' => $this->categoryPlastic->id,
            'address'     => 'CG Road Navrangpura',
        ]);
    }

    /**
     * Server-to-server API call with valid X-Admin-Api-Key succeeds.
     */
    public function test_api_key_header_authentication_succeeds(): void
    {
        $payload = [
            'centres' => [
                [
                    'name'     => 'API Key Hub',
                    'category' => 'Electronic Waste',
                    'city'     => 'Surat',
                    'address'  => 'Station Road',
                ]
            ]
        ];

        $validKey = env('SECRET_KEY', 'dev-wastewise-flask-secret-key-2026');

        $response = $this->withHeaders(['X-Admin-Api-Key' => $validKey])
            ->postJson('/api/admin/collection-centres/import', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success'  => true,
                'imported' => 1,
                'inserted' => 1,
            ]);
    }

    /**
     * Invalid category fails with row-level error.
     */
    public function test_import_fails_on_invalid_category(): void
    {
        $payload = [
            'centres' => [
                [
                    'name'     => 'Invalid Category Hub',
                    'category' => 'Nonexistent Category',
                    'city'     => 'Surat',
                    'address'  => 'Main Street',
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/collection-centres/import', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'failed'  => 1,
            ]);

        $this->assertDatabaseCount('collection_centres', 0);
    }

    /**
     * Missing cities are automatically created via City::firstOrCreate(['name' => $cityName], ['district' => $district]).
     */
    public function test_missing_city_is_auto_created_with_district(): void
    {
        $payload = [
            'centres' => [
                [
                    'name'      => 'Bardoli Rural Recycler',
                    'category'  => 'Electronic Waste',
                    'city'      => 'Bardoli',
                    'district'  => 'Surat District',
                    'address'   => 'Bardoli Main Market',
                    'latitude'  => 21.1190,
                    'longitude' => 73.1120,
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/collection-centres/import', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success'  => true,
                'imported' => 1,
                'inserted' => 1,
            ]);

        $this->assertDatabaseHas('cities', [
            'name'     => 'Bardoli',
            'district' => 'Surat District',
        ]);

        $this->assertDatabaseHas('collection_centres', [
            'name'     => 'Bardoli Rural Recycler',
            'district' => 'Surat District',
        ]);
    }

    /**
     * Non-destructive duplicate detection skips duplicates and increments counters.
     */
    public function test_non_destructive_duplicate_detection_skips_duplicates(): void
    {
        // 1. Existing centre with coordinates and phone
        CollectionCentre::create([
            'name'        => 'Surat Eco Recycler',
            'city_id'     => $this->citySurat->id,
            'category_id' => $this->categoryEWaste->id,
            'address'     => 'Plot 10, Ring Road',
            'phone'       => '9876543210',
            'latitude'    => 21.170243,
            'longitude'   => 72.831100,
        ]);

        $payload = [
            'centres' => [
                // Duplicate 1: Matching normalized name + city
                [
                    'name'     => 'SURAT ECO RECYCLER',
                    'category' => 'Electronic Waste',
                    'city'     => 'Surat',
                    'address'  => 'Plot 10, Ring Road',
                    'phone'    => '9876543210',
                ],
                // Duplicate 2: Within 100m Haversine distance (< 150m)
                [
                    'name'      => 'Nearby Eco Point',
                    'category'  => 'Electronic Waste',
                    'city'      => 'Surat',
                    'address'   => 'Plot 11, Ring Road',
                    'phone'     => '9111222333',
                    'latitude'  => 21.170500, // approx 40 meters away
                    'longitude' => 72.831200,
                ],
                // Fresh new centre:
                [
                    'name'      => 'Fresh New Centre',
                    'category'  => 'Plastic Waste',
                    'city'      => 'Ahmedabad',
                    'address'   => 'Sarkhej Gandhinagar Highway',
                    'latitude'  => 23.0500,
                    'longitude' => 72.5000,
                    'phone'     => '9444555666',
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/collection-centres/import', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success'            => true,
                'inserted'           => 1,
                'skipped_duplicates' => 2,
            ]);

        // Total centres must be 2 (1 pre-existing + 1 new inserted)
        $this->assertDatabaseCount('collection_centres', 2);
    }

    /**
     * Automatic setting of status = 'pending_verification' when authorization expired or latitude is null.
     */
    public function test_auto_status_pending_verification_when_expired_or_coords_null(): void
    {
        $payload = [
            'centres' => [
                // Recycler 1: Null latitude -> pending_verification
                [
                    'name'      => 'Staged Recycler Without GPS',
                    'category'  => 'Electronic Waste',
                    'city'      => 'Surat',
                    'address'   => 'GIDC Sachin',
                    'latitude'  => null,
                    'longitude' => null,
                ],
                // Recycler 2: Expired authorization -> pending_verification
                [
                    'name'                      => 'Expired Recycler',
                    'category'                  => 'Electronic Waste',
                    'city'                      => 'Surat',
                    'address'                   => 'Udhna Industrial Area',
                    'latitude'                  => 21.1500,
                    'longitude'                 => 72.8500,
                    'authorization_valid_until' => Carbon::now()->subDays(10)->format('Y-m-d'),
                ],
                // Recycler 3: Valid future authorization and coordinates -> active
                [
                    'name'                      => 'Active Verified Recycler',
                    'category'                  => 'Electronic Waste',
                    'city'                      => 'Surat',
                    'address'                   => 'Katargam Road',
                    'latitude'                  => 21.2100,
                    'longitude'                 => 72.8300,
                    'authorization_valid_until' => Carbon::now()->addYear()->format('Y-m-d'),
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/collection-centres/import', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success'  => true,
                'inserted' => 3,
            ]);

        $this->assertDatabaseHas('collection_centres', [
            'name'   => 'Staged Recycler Without GPS',
            'status' => 'pending_verification',
        ]);

        $this->assertDatabaseHas('collection_centres', [
            'name'   => 'Expired Recycler',
            'status' => 'pending_verification',
        ]);

        $this->assertDatabaseHas('collection_centres', [
            'name'   => 'Active Verified Recycler',
            'status' => 'active',
        ]);
    }

    /**
     * All-or-nothing transaction rollback when one row is invalid.
     */
    public function test_transaction_rollback_when_one_row_fails(): void
    {
        $payload = [
            'centres' => [
                [
                    'name'     => 'Valid Hub 1',
                    'category' => 'Electronic Waste',
                    'city'     => 'Surat',
                    'address'  => 'Valid Address 1',
                ],
                [
                    'name'     => 'Invalid Hub 2',
                    'category' => 'Nonexistent Category',
                    'city'     => 'Surat',
                    'address'  => 'Address 2',
                ],
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/collection-centres/import', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success'  => false,
                'imported' => 0,
            ]);

        // Rollback check: Valid Hub 1 must NOT exist in DB
        $this->assertDatabaseCount('collection_centres', 0);
    }
}
