<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        Country::factory()
            ->count(10)
            ->has(Region::factory()->count(5), 'regions')
            ->has(City::factory()->count(5), 'cities')
            ->create();
    }
}
