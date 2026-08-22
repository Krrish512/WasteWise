<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            WasteCategorySeeder::class,
            DisposalMethodSeeder::class,
            WasteItemSeeder::class,
            CitySeeder::class,
            CollectionCentreSeeder::class,
        ]);
    }
}