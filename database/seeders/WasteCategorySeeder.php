<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WasteCategory;

class WasteCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronic Waste',
                'description' => 'Discarded electronic devices and accessories like mobiles, laptops, and chargers.',
            ],
            [
                'name' => 'Plastic Waste',
                'description' => 'Non-biodegradable plastic items such as bottles and packaging materials.',
            ],
            [
                'name' => 'Paper Waste',
                'description' => 'Recyclable paper products such as newspapers, magazines, and cardboard.',
            ],
            [
                'name' => 'Glass Waste',
                'description' => 'Glass items such as bottles and jars that can be recycled.',
            ],
            [
                'name' => 'Organic Waste',
                'description' => 'Biodegradable kitchen and food waste suitable for composting.',
            ],
        ];

        foreach ($categories as $category) {
            WasteCategory::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}