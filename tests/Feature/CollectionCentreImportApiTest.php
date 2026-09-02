<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\CollectionCentre;
use App\Models\User;
use App\Models\WasteCategory;
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

        $this->citySurat = City::create(['name' => 'Surat', 'state' => 'Gujarat']);
        $this->cityAhmedabad = City::create(['name' => 'Ahmedabad', 'state' => 'Gujarat']);

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
                'success'  => true,
                'imported' => 2,
                'updated'  => 0,
                'failed'   => 0,
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
     * Invalid city fails with row-level error.
     */
    public function test_import_fails_on_invalid_city(): void
    {
        $payload = [
            'centres' => [
                [
                    'name'     => 'Invalid City Hub',
                    'category' => 'Electronic Waste',
                    'city'     => 'Unknown City',
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
     * Duplicate centre (same name, city, address) updates existing record.
     */
    public function test_duplicate_centre_updates_existing_record(): void
    {
        // Pre-create existing centre
        CollectionCentre::create([
            'name'        => 'Surat Hub',
            'city_id'     => $this->citySurat->id,
            'category_id' => $this->categoryEWaste->id,
            'address'     => 'Ring Road',
            'phone'       => '1111111111',
        ]);

        $payload = [
            'centres' => [
                [
                    'name'     => 'Surat Hub',
                    'category' => 'Electronic Waste',
                    'city'     => 'Surat',
                    'address'  => 'Ring Road',
                    'phone'    => '9999999999',
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/collection-centres/import', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success'  => true,
                'imported' => 0,
                'updated'  => 1,
            ]);

        $this->assertDatabaseCount('collection_centres', 1);
        $this->assertDatabaseHas('collection_centres', [
            'name'  => 'Surat Hub',
            'phone' => '9999999999',
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
                    'category' => 'Electronic Waste',
                    'city'     => 'Nonexistent City',
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
