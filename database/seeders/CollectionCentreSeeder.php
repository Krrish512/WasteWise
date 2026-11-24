<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CollectionCentre;
use App\Models\City;
use App\Models\WasteCategory;

class CollectionCentreSeeder extends Seeder
{
    public function run(): void
    {
        $centres = [
            [
                'city' => 'Surat',
                'district' => 'Surat District',
                'category' => 'Electronic Waste',
                'name' => 'Surat E-Waste Collection Centre',
                'address' => 'Ring Road, Surat, Gujarat',
                'phone' => '9825012345',
                'latitude' => 21.1702,
                'longitude' => 72.8311,
                'facility_type' => 'authorized_recycler',
                'public_access' => 'public_dropoff',
                'authorization_status' => 'verified',
                'authorization_id' => 'GPCB/SUR/EW/2026/01',
                'confidence' => 'high',
                'status' => 'active',
                'notes' => 'Authorized public drop-off for electronics, computers, and lithium-ion batteries.',
            ],
            [
                'city' => 'Surat',
                'district' => 'Surat District',
                'category' => 'Hazardous & Battery Waste',
                'name' => 'Surat Lead Recyclers & Smelters Ltd',
                'address' => 'Plot 45, Sachin GIDC Industrial Area, Surat, Gujarat',
                'phone' => '9825098765',
                'latitude' => 21.0850,
                'longitude' => 72.8620,
                'facility_type' => 'authorized_recycler',
                'public_access' => 'business_industrial_only',
                'authorization_status' => 'verified',
                'authorization_id' => 'GPCB/SUR/HAZ/2026/89',
                'confidence' => 'high',
                'status' => 'active',
                'notes' => 'GPCB authorized lead-acid battery recovery and recycling plant. Strictly accepts automotive and UPS lead-acid batteries.',
            ],
            [
                'city' => 'Surat',
                'district' => 'Surat District',
                'category' => 'Plastic Waste',
                'name' => 'Surat Dry Waste Recycling Unit',
                'address' => 'Athwa Gate, Surat, Gujarat',
                'phone' => '9825023456',
                'latitude' => 21.1959,
                'longitude' => 72.7933,
                'facility_type' => 'municipal_centre',
                'public_access' => 'public_dropoff',
                'authorization_status' => 'verified',
                'authorization_id' => 'SMC/PL/2026/12',
                'confidence' => 'high',
                'status' => 'active',
            ],
            [
                'city' => 'Ahmedabad',
                'district' => 'Ahmedabad District',
                'category' => 'Electronic Waste',
                'name' => 'Ahmedabad Green E-Waste Centre',
                'address' => 'Navrangpura, Ahmedabad, Gujarat',
                'phone' => '9825034567',
                'latitude' => 23.0367,
                'longitude' => 72.5610,
                'facility_type' => 'authorized_recycler',
                'public_access' => 'public_dropoff',
                'authorization_status' => 'verified',
                'authorization_id' => 'GPCB/AHM/EW/2026/05',
                'confidence' => 'high',
                'status' => 'active',
                'notes' => 'Certified take-back hub for lithium-ion battery packs, mobiles, and electronic gadgets.',
            ],
            [
                'city' => 'Ahmedabad',
                'district' => 'Ahmedabad District',
                'category' => 'Hazardous & Battery Waste',
                'name' => 'Gujarat State Battery Smelters & Buyback',
                'address' => 'Naroda GIDC Phase 2, Ahmedabad, Gujarat',
                'phone' => '9825087654',
                'latitude' => 23.0720,
                'longitude' => 72.6640,
                'facility_type' => 'authorized_recycler',
                'public_access' => 'pickup_provider',
                'authorization_status' => 'verified',
                'authorization_id' => 'GPCB/AHM/BAT/2026/22',
                'confidence' => 'high',
                'status' => 'active',
                'notes' => 'Authorized dealer network for car, inverter, and industrial lead-acid batteries with doorstep bulk pickup.',
            ],
            [
                'city' => 'Ahmedabad',
                'district' => 'Ahmedabad District',
                'category' => 'Organic Waste',
                'name' => 'Ahmedabad Composting Facility',
                'address' => 'Bopal, Ahmedabad, Gujarat',
                'phone' => '9825045678',
                'latitude' => 23.0324,
                'longitude' => 72.4708,
                'facility_type' => 'processing_facility',
                'public_access' => 'public_dropoff',
                'authorization_status' => 'verified',
                'status' => 'active',
            ],
            [
                'city' => 'Vadodara',
                'district' => 'Vadodara District',
                'category' => 'Paper Waste',
                'name' => 'Vadodara Paper Recycling Centre',
                'address' => 'Alkapuri, Vadodara, Gujarat',
                'phone' => '9825056789',
                'latitude' => 22.3095,
                'longitude' => 73.1812,
                'facility_type' => 'municipal_centre',
                'public_access' => 'public_dropoff',
                'authorization_status' => 'verified',
                'status' => 'active',
            ],
            [
                'city' => 'Rajkot',
                'district' => 'Rajkot District',
                'category' => 'Glass Waste',
                'name' => 'Rajkot Glass Collection Point',
                'address' => 'Yagnik Road, Rajkot, Gujarat',
                'phone' => '9825067890',
                'latitude' => 22.2965,
                'longitude' => 70.7984,
                'facility_type' => 'public_dropoff',
                'public_access' => 'public_dropoff',
                'authorization_status' => 'verified',
                'status' => 'active',
            ],
            [
                'city' => 'Gandhinagar',
                'district' => 'Gandhinagar District',
                'category' => 'Electronic Waste',
                'name' => 'Gandhinagar Digital Eco-Hub',
                'address' => 'Sector 11, Gandhinagar, Gujarat',
                'phone' => '9825078901',
                'latitude' => 23.2156,
                'longitude' => 72.6369,
                'facility_type' => 'public_dropoff',
                'public_access' => 'public_dropoff',
                'authorization_status' => 'verified',
                'status' => 'active',
            ],
            [
                'city' => 'Bhavnagar',
                'district' => 'Bhavnagar District',
                'category' => 'Plastic Waste',
                'name' => 'Bhavnagar Plastic Sorting Unit',
                'address' => 'Waghawadi Road, Bhavnagar, Gujarat',
                'phone' => '9825089012',
                'latitude' => 21.7645,
                'longitude' => 72.1519,
                'facility_type' => 'municipal_centre',
                'public_access' => 'public_dropoff',
                'authorization_status' => 'verified',
                'status' => 'active',
            ],
            [
                'city' => 'Jamnagar',
                'district' => 'Jamnagar District',
                'category' => 'Organic Waste',
                'name' => 'Jamnagar Eco-Compost Yard',
                'address' => 'Khambhalia Gate, Jamnagar, Gujarat',
                'phone' => '9825090123',
                'latitude' => 22.4707,
                'longitude' => 70.0577,
                'facility_type' => 'processing_facility',
                'public_access' => 'public_dropoff',
                'authorization_status' => 'verified',
                'status' => 'active',
            ],
            [
                'city' => 'Junagadh',
                'district' => 'Junagadh District',
                'category' => 'Paper Waste',
                'name' => 'Junagadh Paper Salvage Hub',
                'address' => 'Zanzarda Road, Junagadh, Gujarat',
                'phone' => '9825001234',
                'latitude' => 21.5222,
                'longitude' => 70.4579,
                'facility_type' => 'public_dropoff',
                'public_access' => 'public_dropoff',
                'authorization_status' => 'verified',
                'status' => 'active',
            ],
        ];

        foreach ($centres as $centre) {
            $city = City::firstOrCreate(
                ['name' => $centre['city']],
                ['state' => 'Gujarat', 'district' => $centre['district'] ?? null]
            );

            if (!empty($centre['district']) && empty($city->district)) {
                $city->update(['district' => $centre['district']]);
            }

            $category = WasteCategory::where('name', $centre['category'])->first();

            if ($category) {
                $c = CollectionCentre::updateOrCreate(
                    [
                        'name' => $centre['name'],
                        'city_id' => $city->id,
                    ],
                    [
                        'category_id' => $category->id,
                        'district' => $centre['district'] ?? $city->district,
                        'address' => $centre['address'],
                        'phone' => $centre['phone'],
                        'latitude' => $centre['latitude'],
                        'longitude' => $centre['longitude'],
                        'facility_type' => $centre['facility_type'] ?? 'public_dropoff',
                        'public_access' => $centre['public_access'] ?? 'public_dropoff',
                        'authorization_status' => $centre['authorization_status'] ?? 'verified',
                        'authorization_id' => $centre['authorization_id'] ?? null,
                        'confidence' => $centre['confidence'] ?? 'high',
                        'status' => $centre['status'] ?? 'active',
                        'notes' => $centre['notes'] ?? null,
                    ]
                );

                $c->categories()->sync([$category->id]);
            }
        }
    }
}
