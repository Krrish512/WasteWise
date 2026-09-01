<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KnowledgeArticle;
use App\Models\WasteCategory;
use Illuminate\Support\Carbon;

class KnowledgeArticleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Reuse or create categories safely without duplication
        $electronic = WasteCategory::firstOrCreate(
            ['name' => 'Electronic Waste'],
            ['description' => 'Discarded electronic devices and accessories like mobiles, laptops, and chargers.']
        );

        $plastic = WasteCategory::firstOrCreate(
            ['name' => 'Plastic Waste'],
            ['description' => 'Non-biodegradable plastic items such as bottles and packaging materials.']
        );

        $paper = WasteCategory::firstOrCreate(
            ['name' => 'Paper Waste'],
            ['description' => 'Recyclable paper products such as newspapers, magazines, and cardboard.']
        );

        $glass = WasteCategory::firstOrCreate(
            ['name' => 'Glass Waste'],
            ['description' => 'Glass items such as bottles and jars that can be recycled.']
        );

        $organic = WasteCategory::firstOrCreate(
            ['name' => 'Organic Waste'],
            ['description' => 'Biodegradable kitchen and food waste suitable for composting.']
        );

        $metal = WasteCategory::firstOrCreate(
            ['name' => 'Metal Waste'],
            ['description' => 'Recyclable metal items such as aluminum cans, tin, and scrap metal.']
        );

        $articles = [
            [
                'title' => 'How to Dispose of Old Laptops Safely',
                'slug' => 'how-to-dispose-of-old-laptops-safely',
                'waste_category_id' => $electronic->id,
                'summary' => 'Laptops contain hazardous heavy metals like lead and mercury, as well as sensitive personal data. Learn the step-by-step process to back up your data, wipe your drive, and safely drop off your laptop at designated e-waste recycling centres.',
                'content' => "Laptops contain valuable minerals like gold, copper, and aluminum, alongside hazardous materials including lithium, mercury, and lead. Disposing of laptops in regular household garbage contaminates soil and water systems.\n\nStep 1: Back Up Your Personal Data\nBefore letting go of your device, back up essential photos, documents, and credentials to an external drive or cloud storage.\n\nStep 2: Wipe Your Hard Drive Completely\nSimply deleting files or emptying the Recycle Bin is not enough to protect your privacy. Perform a full system reset or use disk-wiping tools (such as DBAN or built-in OS drive erasure) to permanently sanitize all stored information.\n\nStep 3: Remove the Battery (If Removable)\nLithium-ion batteries pose fire risks if damaged or crushed. If your laptop has an easily accessible removable battery, remove it and place tape over the contact terminals.\n\nStep 4: Take to an Authorized E-Waste Collection Centre\nDrop off the laptop and power adapter at an authorized e-waste collection centre or manufacturer trade-in program where materials can be recycled safely into new products.",
                'is_published' => true,
                'published_at' => $now,
            ],
            [
                'title' => 'Safe Disposal of Old Mobile Phones',
                'slug' => 'safe-disposal-of-old-mobile-phones',
                'waste_category_id' => $electronic->id,
                'summary' => 'Mobile phones are packed with precious metals and compact lithium batteries. Discover how to prepare your phone for recycling, back up your photos, perform a factory reset, and find drop-off locations.',
                'content' => "Millions of old smartphones sit unused in drawers or end up in landfills. Smart disposal ensures precious metals are recovered and hazardous chemicals are safely contained.\n\n1. Remove SIM & Memory Cards\nTake out your SIM card and any expandable microSD storage cards before passing on or recycling your phone.\n\n2. Log Out & Perform Factory Reset\nUnlink your Apple ID or Google Account, disable security tracking features like 'Find My Phone', and perform a full factory data reset.\n\n3. Donate or Trade-In Working Phones\nIf your phone is still functional, consider giving it a second life through donation to local schools or trading it in for credit.\n\n4. Recycle Damaged Devices\nNon-functional phones should be delivered to e-waste recycling bins or collection points. Recyclers dismantle devices to extract gold, silver, copper, and palladium safely.",
                'is_published' => true,
                'published_at' => $now,
            ],
            [
                'title' => 'How to Handle Used Batteries Responsibly',
                'slug' => 'how-to-handle-used-batteries-responsibly',
                'waste_category_id' => $electronic->id,
                'summary' => 'Household and rechargeable batteries contain corrosive chemicals that can cause fires and environmental damage if thrown in regular trash. Learn safe storage and disposal methods.',
                'content' => "Batteries are among the most common household items improperly discarded in municipal trash. Correct handling prevents dangerous sparks and chemical leakage.\n\nWhy Batteries Cannot Go in General Trash\nWhen crushed inside garbage trucks or landfills, lithium and alkaline batteries can rupture and spark fires. Toxic chemicals like nickel, cadmium, and cobalt can also leach into surrounding groundwater.\n\nSafe Storage at Home\nStore spent batteries in a cool, dry place inside a non-conductive plastic container. Apply clear electrical tape over the positive and negative terminals of 9V and lithium-ion batteries to prevent short-circuiting.\n\nProper Disposal Points\nTake accumulated batteries to community hazardous waste drop-off locations, battery collection boxes in retail electronic stores, or WasteWise certified collection facilities.",
                'is_published' => true,
                'published_at' => $now,
            ],
            [
                'title' => 'How to Reduce Single-Use Plastic',
                'slug' => 'how-to-reduce-single-use-plastic',
                'waste_category_id' => $plastic->id,
                'summary' => 'Single-use plastics are major contributors to ocean pollution and landfill buildup. Explore practical daily habits to replace disposable plastic items with eco-friendly reusable alternatives.',
                'content' => "Single-use plastics are designed to be thrown away after just one brief use, yet they persist in the environment for hundreds of years. Reducing your reliance on disposable plastic starts with small daily choices.\n\n1. Carry Reusable Bags & Water Bottles\nKeep durable cloth tote bags in your vehicle or backpack, and carry a stainless steel or glass refillable water bottle wherever you go.\n\n2. Say No to Disposable Cutlery & Straws\nPolystyrene containers and plastic utensils cannot easily be recycled. Keep a compact set of bamboo or metal cutlery in your bag for takeout meals.\n\n3. Choose Bulk and Minimalist Packaging\nShop at local markets or choose food items packaged in recyclable paper, cardboard, or glass rather than multi-layered plastic wraps.\n\n4. Store Food in Glass or Stainless Containers\nReplace plastic cling film and disposable zip bags with washable silicone pouches, beeswax wraps, or glass containers for kitchen food storage.",
                'is_published' => true,
                'published_at' => $now,
            ],
            [
                'title' => 'How to Recycle Plastic Bottles',
                'slug' => 'how-to-recycle-plastic-bottles',
                'waste_category_id' => $plastic->id,
                'summary' => 'Not all plastic bottles are processed the same way. Learn how to identify resin codes (PET, HDPE), rinse bottles correctly, and prepare them for efficient curb-side or center recycling.',
                'content' => "Plastic bottles made from PET (#1) and HDPE (#2) are widely recyclable, but improper preparation can cause entire recycling loads to be rejected as contaminated.\n\nCheck the Resin Identification Code\nLook at the recycling triangle code at the bottom of the bottle. PET (Polyethylene Terephthalate) and HDPE (High-Density Polyethylene) are the most commonly accepted plastics.\n\nRinse and Drain\nLightly rinse bottles to remove residual liquids like soda, milk, or detergent. Leftover organic matter generates odors and attracts pests at recycling hubs.\n\nKeep Caps On or Flatten Bottles\nMost modern recycling processing facilities prefer bottles flattened with caps screwed back on. Check local guidelines on whether caps should be separated.\n\nAvoid Bagging Recyclables\nDo not place plastic bottles inside tied black trash bags when dropping them off at collection points, as closed bags are often discarded unopened during sorting.",
                'is_published' => true,
                'published_at' => $now,
            ],
            [
                'title' => 'How to Recycle Newspapers and Paper',
                'slug' => 'how-to-recycle-newspapers-and-paper',
                'waste_category_id' => $paper->id,
                'summary' => 'Paper is one of the most easily recyclable materials. Discover best practices for sorting newspapers, cardboard, and office paper, and learn which paper products cannot be recycled.',
                'content' => "Recycling paper saves trees, reduces water usage, and conserves significant amounts of energy compared to producing virgin paper from wood pulp.\n\nWhat Can Be Recycled\n- Daily newspapers and inserts\n- Corrugated cardboard boxes (flattened)\n- Office paper, letters, and envelopes\n- Magazines and paperback books\n\nWhat Cannot Be Recycled\n- Greasy pizza boxes or food-contaminated cardboard\n- Waxed paper, thermal receipt paper, and metallic wrapping paper\n- Used tissues, paper towels, and sanitary paper products\n\nPreparation Steps\nKeep paper dry and clean. Flatten all cardboard boxes to save space in collection bins and prevent paper fibers from soaking in rainwater or food grease.",
                'is_published' => true,
                'published_at' => $now,
            ],
            [
                'title' => 'Safe Disposal and Recycling of Glass',
                'slug' => 'safe-disposal-and-recycling-of-glass',
                'waste_category_id' => $glass->id,
                'summary' => 'Glass is 100% recyclable and can be processed infinitely without loss of quality. Learn how to safely handle glass bottles, jars, and broken glass fragments.',
                'content' => "Unlike plastic, glass does not degrade over multiple recycling cycles. Recycling a single glass bottle saves enough energy to power a computer for 25 minutes.\n\nAccepted Glass Types\nClear, green, and brown food bottles, beverage containers, and condiment jars are fully recyclable.\n\nNon-Recyclable Specialty Glass\nLight bulbs, ceramic cookware, drinking glasses, window panes, and mirrors have different melting points and chemical compositions. Do not mix them with container glass.\n\nHow to Prepare Glass Containers\nRinse out all food remnants. Metal lids and corks should be removed and placed in their respective recycling streams (metal or compost).\n\nHandling Broken Glass Safely\nIf glass is broken, wrap it securely in thick newspaper or cardboard before placing it in non-recyclable trash to protect sanitation workers from injury.",
                'is_published' => true,
                'published_at' => $now,
            ],
            [
                'title' => 'Simple Ways to Manage Kitchen and Organic Waste',
                'slug' => 'simple-ways-to-manage-kitchen-and-organic-waste',
                'waste_category_id' => $organic->id,
                'summary' => 'Kitchen scraps and organic food waste account for nearly half of household waste. Learn easy home composting techniques and wet waste management tips to enrich soil.',
                'content' => "Organic waste buried in anaerobic landfills decomposes to produce methane gas, a potent greenhouse driver. Composting turns food waste into nutrient-dense organic fertilizer.\n\nGreen Materials (Nitrogen Rich)\nVegetable peels, fruit scraps, coffee grounds, tea leaves, and fresh lawn clippings provide nitrogen needed by composting microbes.\n\nBrown Materials (Carbon Rich)\nDry leaves, shredded cardboard, egg cartons, sawdust, and straw provide carbon and create aeration pockets in your compost bin.\n\nItems to Keep Out of Home Compost\nAvoid adding meat, dairy products, cooking oil, bones, or pet waste to basic home compost bins, as they attract pests and generate unpleasant odors.\n\nStarting Your Household Compost\nUse a ventilated outdoor bin or an indoor vermicomposting (worm) box. Layer green and brown materials in a 1:2 ratio and keep the pile moist like a damp sponge.",
                'is_published' => true,
                'published_at' => $now,
            ],
            [
                'title' => 'How to Recycle Household Metal Waste',
                'slug' => 'how-to-recycle-household-metal-waste',
                'waste_category_id' => $metal->id,
                'summary' => 'Aluminum beverage cans, steel food tins, and scrap metal have high recycling value. Discover how metal recycling saves up to 95% of energy compared to raw mining.',
                'content' => "Metals are among the most valuable commodities in the recycling industry. Aluminum cans can be melted down and returned to store shelves as new cans in as little as 60 days.\n\nCommon Recyclable Metals\n- Aluminum soda and juice cans\n- Steel and tin soup/vegetable cans\n- Clean aluminum foil and pie pans\n- Small metal appliances, pots, pans, and hardware\n\nPreparation Guidelines\nEmpty and lightly rinse food tins. Push metal lids inside the cans or crush cans down to optimize transport volume.\n\nScrap Metal Drop-offs\nLarger items such as metal furniture frames, copper piping, or old iron cookware should be taken to scrap yards or local waste collection centres for weight-based recycling.",
                'is_published' => true,
                'published_at' => $now,
            ],
            [
                'title' => 'How to Separate Waste at Home',
                'slug' => 'how-to-separate-waste-at-home',
                'waste_category_id' => $organic->id,
                'summary' => 'Effective source segregation at home is the foundation of successful city-wide recycling. Learn how to set up a clean 3-bin system for dry, wet, and hazardous waste.',
                'content' => "When recyclables are mixed with wet kitchen garbage, they become contaminated and end up in landfills. Sorting waste at the source makes recycling efficient and hygienic.\n\nThe Three-Bin Household System\n\n1. Green Bin (Wet & Organic Waste)\nUse for food scraps, fruit peels, leftover meals, tea bags, and garden leaves. This waste should be composted or sent to bio-degradable waste facilities.\n\n2. Blue Bin (Dry Recyclables)\nUse for clean paper, cardboard, plastic bottles, metal cans, glass jars, and dry packaging. Ensure items are clean and dry before tossing them into the blue bin.\n\n3. Red Bin (Domestic Hazardous Waste)\nUse for expired medicines, used batteries, broken light bulbs, paints, cleaning chemicals, and sanitary waste. Keep these isolated from regular trash.\n\nTips for Sustained Habits\nLabel your household bins clearly and place them near high-waste areas like the kitchen. Educate all family members on basic sorting rules.",
                'is_published' => true,
                'published_at' => $now,
            ],
        ];

        foreach ($articles as $article) {
            KnowledgeArticle::updateOrCreate(
                ['slug' => $article['slug']],
                $article
            );
        }
    }
}
