<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WasteItem;
use App\Models\WasteCategory;
use App\Models\DisposalMethod;

class WasteItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Old Mobile',
                'category' => 'Electronic Waste',
                'method' => 'E-Waste Recycling Centre',
                'description' => 'A used or broken mobile phone that is no longer functional.',
                'keywords' => 'mobile, phone, smartphone, cell phone',
            ],
            [
                'name' => 'Old Laptop',
                'category' => 'Electronic Waste',
                'method' => 'E-Waste Recycling Centre',
                'description' => 'An outdated or non-working laptop computer.',
                'keywords' => 'laptop, notebook, computer',
            ],
            [
                'name' => 'Old Charger',
                'category' => 'Electronic Waste',
                'method' => 'E-Waste Recycling Centre',
                'description' => 'A damaged or unused mobile/laptop charger.',
                'keywords' => 'charger, adapter, cable',
            ],
            [
                'name' => 'Plastic Bottle',
                'category' => 'Plastic Waste',
                'method' => 'Dry Waste Recycling',
                'description' => 'Empty plastic bottles used for water or beverages.',
                'keywords' => 'plastic, bottle, water bottle, PET',
            ],
            [
                'name' => 'Newspaper',
                'category' => 'Paper Waste',
                'method' => 'Paper Recycling',
                'description' => 'Old newspapers collected over time.',
                'keywords' => 'newspaper, paper, news',
            ],
            [
                'name' => 'Glass Bottle',
                'category' => 'Glass Waste',
                'method' => 'Glass Recycling',
                'description' => 'Empty glass bottles from beverages or sauces.',
                'keywords' => 'glass, bottle, jar',
            ],
            [
                'name' => 'Food Waste',
                'category' => 'Organic Waste',
                'method' => 'Composting',
                'description' => 'Leftover or spoiled food and kitchen scraps.',
                'keywords' => 'food, kitchen waste, leftovers, vegetable peels',
            ],
        ];

        foreach ($items as $item) {
            $category = WasteCategory::where('name', $item['category'])->first();
            $method = DisposalMethod::where('name', $item['method'])->first();

            if ($category && $method) {
                WasteItem::firstOrCreate(
                    ['name' => $item['name']],
                    [
                        'category_id' => $category->id,
                        'disposal_method_id' => $method->id,
                        'description' => $item['description'],
                        'keywords' => $item['keywords'],
                    ]
                );
            }
        }
    }
}