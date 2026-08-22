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
                'category' => 'Electronic Waste',
                'name' => 'Surat E-Waste Collection Centre',
                'address' => 'Ring Road, Surat, Gujarat',
                'phone' => '9825012345',
                'latitude' => 21.1702,
                'longitude' => 72.8311,
            ],
            [
                'city' => 'Surat',
                'category' => 'Plastic Waste',
                'name' => 'Surat Dry Waste Recycling Unit',
                'address' => 'Athwa Gate, Surat, Gujarat',
                'phone' => '9825023456',
                'latitude' => 21.1959,
                'longitude' => 72.7933,
            ],
            [
                'city' => 'Ahmedabad',
                'category' => 'Electronic Waste',
                'name' => 'Ahmedabad Green E-Waste Centre',
                'address' => 'Navrangpura, Ahmedabad, Gujarat',
                'phone' => '9825034567',
                'latitude' => 23.0367,
                'longitude' => 72.5610,
            ],
            [
                'city' => 'Ahmedabad',
                'category' => 'Organic Waste',
                'name' => 'Ahmedabad Composting Facility',
                'address' => 'Bopal, Ahmedabad, Gujarat',
                'phone' => '9825045678',
                'latitude' => 23.0324,
                'longitude' => 72.4708,
            ],
            [
                'city' => 'Vadodara',
                'category' => 'Paper Waste',
                'name' => 'Vadodara Paper Recycling Centre',
                'address' => 'Alkapuri, Vadodara, Gujarat',
                'phone' => '9825056789',
                'latitude' => 22.3095,
                'longitude' => 73.1812,
            ],
            [
                'city' => 'Vadodara',
                'category' => 'Glass Waste',
                'name' => 'Vadodara Glass Collection Point',
                'address' => 'Sayajigunj, Vadodara, Gujarat',
                'phone' => '9825067890',
                'latitude' => 22.3149,
                'longitude' => 73.1926,
            ],
            [
                'city' => 'Rajkot',
                'category' => 'Plastic Waste',
                'name' => 'Rajkot Plastic Recycling Hub',
                'address' => 'Kalawad Road, Rajkot, Gujarat',
                'phone' => '9825078901',
                'latitude' => 22.3039,
                'longitude' => 70.8022,
            ],
            [
                'city' => 'Rajkot',
                'category' => 'Organic Waste',
                'name' => 'Rajkot Organic Waste Composting Site',
                'address' => 'Race Course, Rajkot, Gujarat',
                'phone' => '9825089012',
                'latitude' => 22.3039,
                'longitude' => 70.8022,
            ],
            [
                'city' => 'Bhavnagar',
                'category' => 'Electronic Waste',
                'name' => 'Bhavnagar E-Waste Collection Hub',
                'address' => 'Waghwadi, Bhavnagar, Gujarat',
                'phone' => '9825090123',
                'latitude' => 21.7645,
                'longitude' => 72.1519,
            ],
            [
                'city' => 'Bhavnagar',
                'category' => 'Paper Waste',
                'name' => 'Bhavnagar Paper Recycling Facility',
                'address' => 'Palitana Road, Bhavnagar, Gujarat',
                'phone' => '9825101234',
                'latitude' => 21.7645,
                'longitude' => 72.1519,
            ],
            [
                'city' => 'Bhavnagar',
                'category' => 'Glass Waste',
                'name' => 'Bhavnagar Glass Collection Point',
                'address' => 'Sardar Patel Road, Bhavnagar, Gujarat',
                'phone' => '9825112345',
                'latitude' => 21.7645,
                'longitude' => 72.1519,
            ],
            [
                'city' => 'Bhavnagar',
                'category' => 'Organic Waste',
                'name' => 'Bhavnagar Organic Waste Composting Site',
                'address' => 'Ghogha Road, Bhavnagar, Gujarat',
                'phone' => '9825123456',
                'latitude' => 21.7645,
                'longitude' => 72.1519,
            ],[
                
                'city' => 'surat',
                'category' => 'Plastic Waste',
                'name' => 'Surat Plastic Recycling Hub',
                'address' => 'Waghwadi, Surat, Gujarat',
                'phone' => '9825134567',
                'latitude' => 21.7645,
                'longitude' => 72.1519,
            ]
        ];

        foreach ($centres as $centre) {
            $city = City::where('name', $centre['city'])->first();
            $category = WasteCategory::where('name', $centre['category'])->first();

            if ($city && $category) {
                CollectionCentre::firstOrCreate(
                    ['name' => $centre['name']],
                    [
                        'city_id' => $city->id,
                        'category_id' => $category->id,
                        'address' => $centre['address'],
                        'phone' => $centre['phone'],
                        'latitude' => $centre['latitude'],
                        'longitude' => $centre['longitude'],
                    ]
                );
            }
        }
    }
}