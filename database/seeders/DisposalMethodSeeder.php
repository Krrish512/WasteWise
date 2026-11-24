<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DisposalMethod;

class DisposalMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name' => 'E-Waste Recycling Centre',
                'instructions' => 'Drop off the device at an authorized e-waste collection centre. Data should be wiped before disposal.',
                'avoid' => 'Do not throw in regular household dustbins or burn electronic items.',
            ],
            [
                'name' => 'GPCB Lead-Acid Battery Recycler',
                'instructions' => 'Return spent lead-acid battery to an authorized battery dealer, OEM buyback center, or GPCB-authorized lead recycler under Battery Waste Management Rules.',
                'avoid' => 'Never dump battery acid into drains or sell to informal scrap dealers—severe lead toxicity and acid burn hazard.',
            ],
            [
                'name' => 'Lithium-Ion Battery Take-Back',
                'instructions' => 'Take to authorized e-waste collection centre or manufacturer take-back kiosk with taped terminals.',
                'avoid' => 'Never crush, puncture, or send Lithium-Ion batteries to Lead-Acid scrap dealers—fire and toxic gas hazard; use authorized e-waste / Li-ion take-back channels.',
            ],
            [
                'name' => 'Dry Waste Recycling',
                'instructions' => 'Clean and dry the item, then place it in the blue/dry waste bin for recycling.',
                'avoid' => 'Do not mix with wet or food waste.',
            ],
            [
                'name' => 'Paper Recycling',
                'instructions' => 'Bundle newspapers and paper items together and give to local kabadiwala or recycling centre.',
                'avoid' => 'Avoid wetting the paper or mixing it with plastic waste.',
            ],
            [
                'name' => 'Glass Recycling',
                'instructions' => 'Rinse glass bottles and place them in designated glass recycling bins.',
                'avoid' => 'Do not dispose of broken glass with regular waste; wrap it safely first.',
            ],
            [
                'name' => 'Composting',
                'instructions' => 'Collect food waste in a compost bin to convert it into organic manure.',
                'avoid' => 'Avoid adding plastic, metal, or chemical waste into compost.',
            ],
        ];

        foreach ($methods as $method) {
            DisposalMethod::firstOrCreate(
                ['name' => $method['name']],
                [
                    'instructions' => $method['instructions'],
                    'avoid' => $method['avoid'],
                ]
            );
        }
    }
}
