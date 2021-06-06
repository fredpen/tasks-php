<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = Country::factory()
            ->has(Region::factory()->count(10), 'regions')
            ->create();
        // $this->call([
        //     UserSeeder::class,
        //     UserSeeder::class,
        //     PostSeeder::class,
        //     CommentSeeder::class,
        // ]);
    }
}
