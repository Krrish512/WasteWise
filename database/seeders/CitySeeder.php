<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name' => 'Surat', 'state' => 'Gujarat'],
            ['name' => 'Ahmedabad', 'state' => 'Gujarat'],
            ['name' => 'Vadodara', 'state' => 'Gujarat'],
            ['name' => 'Rajkot', 'state' => 'Gujarat'],
            ['name' => 'Bhavnagar', 'state' => 'Gujarat'],
            ['name' => 'Jamnagar', 'state' => 'Gujarat'],
            ['name' => 'Junagadh', 'state' => 'Gujarat'],
            ['name' => 'Gandhinagar', 'state' => 'Gujarat'],
            ['name' => 'Anand', 'state' => 'Gujarat'],
            ['name' => 'Nadiad', 'state' => 'Gujarat'],
            ['name' => 'Vapi', 'state' => 'Gujarat'],
            ['name' => 'Valsad', 'state' => 'Gujarat'],
            ['name' => 'Navsari', 'state' => 'Gujarat'],
            ['name' => 'Surendranagar', 'state' => 'Gujarat'],
            ['name' => 'Patan', 'state' => 'Gujarat'],
            ['name' => 'Mehsana', 'state' => 'Gujarat'],
            ['name' => 'Bharuch', 'state' => 'Gujarat'],
            ['name' => 'Dahod', 'state' => 'Gujarat'],
            ['name' => 'Narmada', 'state' => 'Gujarat'],
            ['name' => 'Kheda', 'state' => 'Gujarat'],
            ['name' => 'Dangs', 'state' => 'Gujarat'],
            ['name' => 'Aravalli', 'state' => 'Gujarat'],
            ['name' => 'Botad', 'state' => 'Gujarat'],
            ['name' => 'Chhota Udaipur', 'state' => 'Gujarat'],
            ['name' => 'Devbhoomi Dwarka', 'state' => 'Gujarat'],
            ['name' => 'Gir Somnath', 'state' => 'Gujarat'],
            ['name' => 'Mahisagar', 'state' => 'Gujarat'],
            ['name' => 'Morbi', 'state' => 'Gujarat'],
            ['name' => 'Panchmahal', 'state' => 'Gujarat'],
            ['name' => 'Porbandar', 'state' => 'Gujarat'],
            ['name' => 'Surat', 'state' => null],
        ];

        foreach ($cities as $city) {
            City::firstOrCreate(
                ['name' => $city['name']],
                ['state' => $city['state']]
            );
        }
    }
}