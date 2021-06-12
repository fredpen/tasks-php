<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $countriesPath = database_path('seeders/sqls/countries.sql');
        $regionsPath = database_path('seeders/sqls/states.sql');
        $cityPath = database_path('seeders/sqls/cities.sql');

        $countriesSql = file_get_contents($countriesPath);
        $regionsPath = file_get_contents($regionsPath);
        $cityPath = file_get_contents($cityPath);

        DB::unprepared($countriesSql);
        DB::unprepared($regionsPath);
        DB::unprepared($cityPath);

        // Country::factory()
        //     ->count(10)
        //     ->has(Region::factory()->count(5), 'regions')
        //     ->has(City::factory()->count(5), 'cities')
        //     ->create();
    }
}
