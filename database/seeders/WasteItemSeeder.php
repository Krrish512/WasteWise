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
                'keywords' => 'mobile, phone, smartphone, cell phone, mobile phone, cellphone, android, iphone, telephone, broken phone, old mobile phone',
            ],
            [
                'name' => 'Old Laptop',
                'category' => 'Electronic Waste',
                'method' => 'E-Waste Recycling Centre',
                'description' => 'An outdated or non-working laptop computer.',
                'keywords' => 'laptop, notebook, computer, pc, macbook, broken laptop, old computer, laptop pc',
            ],
            [
                'name' => 'Old Charger',
                'category' => 'Electronic Waste',
                'method' => 'E-Waste Recycling Centre',
                'description' => 'A damaged or unused mobile/laptop charger.',
                'keywords' => 'charger, adapter, cable, wire, power cord, phone charger',
            ],
            [
                'name' => 'Plastic Bottle',
                'category' => 'Plastic Waste',
                'method' => 'Dry Waste Recycling',
                'description' => 'Empty plastic bottles used for water or beverages.',
                'keywords' => 'plastic, bottle, water bottle, PET, plastic bottles, soda bottle, beverage bottle',
            ],
            [
                'name' => 'Newspaper',
                'category' => 'Paper Waste',
                'method' => 'Paper Recycling',
                'description' => 'Old newspapers collected over time.',
                'keywords' => 'newspaper, paper, news, newspapers, paper waste, raddi, paper bundle',
            ],
            [
                'name' => 'Glass Bottle',
                'category' => 'Glass Waste',
                'method' => 'Glass Recycling',
                'description' => 'Empty glass bottles from beverages or sauces.',
                'keywords' => 'glass, bottle, jar, glass bottle, glass bottles, wine bottle',
            ],
            [
                'name' => 'Food Waste',
                'category' => 'Organic Waste',
                'method' => 'Composting',
                'description' => 'Leftover or spoiled food and kitchen scraps.',
                'keywords' => 'food, kitchen waste, leftovers, vegetable peels, organic, wet waste, food scraps',
            ],
            [
                'name' => 'Cardboard Box',
                'category' => 'Paper Waste',
                'method' => 'Paper Recycling',
                'description' => 'Corrugated cardboard boxes and shipping packaging.',
                'keywords' => 'cardboard, box, packaging, carton, shipping box, paper box',
            ],
            [
                'name' => 'Plastic Container',
                'category' => 'Plastic Waste',
                'method' => 'Dry Waste Recycling',
                'description' => 'Food storage plastic containers, tubs, and packaging.',
                'keywords' => 'plastic, container, tub, tupperware, plastic box, food container',
            ],
            [
                'name' => 'Vegetable Peels',
                'category' => 'Organic Waste',
                'method' => 'Composting',
                'description' => 'Raw vegetable and fruit peels from kitchen preparation.',
                'keywords' => 'vegetable, peels, fruit peels, organic waste, kitchen scraps',
            ],
            [
                'name' => 'Glass Jar',
                'category' => 'Glass Waste',
                'method' => 'Glass Recycling',
                'description' => 'Clean food storage glass jars and containers.',
                'keywords' => 'glass, jar, jam jar, glass container, pickle jar',
            ],
            [
                'name' => 'Old Tablet',
                'category' => 'Electronic Waste',
                'method' => 'E-Waste Recycling Centre',
                'description' => 'Unused or broken touchscreen tablet devices.',
                'keywords' => 'tablet, ipad, screen, gadget, electronic, android tablet',
            ],
            [
                'name' => 'Batteries',
                'category' => 'Electronic Waste',
                'method' => 'E-Waste Recycling Centre',
                'description' => 'Used household batteries including AA, AAA, and rechargeable cells.',
                'keywords' => 'battery, batteries, cells, aa, aaa, rechargeable, lithium',
            ],
            [
                'name' => 'Magazines',
                'category' => 'Paper Waste',
                'method' => 'Paper Recycling',
                'description' => 'Old glossy magazines, brochures, and catalogs.',
                'keywords' => 'magazine, magazines, book, catalog, brochure, printed paper',
            ],
        ];

        foreach ($items as $item) {
            $category = WasteCategory::where('name', $item['category'])->first();
            $method = DisposalMethod::where('name', $item['method'])->first();

            if ($category && $method) {
                WasteItem::updateOrCreate(
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