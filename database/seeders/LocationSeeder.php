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
        $path = database_path('seeders/sqls/countries.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);

        // Country::factory()
        //     ->count(10)
        //     ->has(Region::factory()->count(5), 'regions')
        //     ->has(City::factory()->count(5), 'cities')
        //     ->create();
    }
}
