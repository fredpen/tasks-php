<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserSkills;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{

    public function run()
    {
        User::factory()
            ->has(UserSkills::factory()->count(5), 'skills')
            ->count(15)
            ->create();

        // admin
        User::factory()
            ->has(UserSkills::factory()->count(5), 'skills')
            ->create(
                [
                    'name' => "Admin Admin",
                    'email' => "fred@gmail.com",
                    "isActive" => 1,
                    "role_id" => 0,
                    "title" => "Software developer",
                    "country_id" => 1,
                    "region_id" => 1,
                    "city_id" => 1,
                    "bio" => "In all, Know thyself",
                    "address" => "Lagos Nigeria",
                    "identification" => "someurl",
                    "avatar" => "someurl",

                ]
            );
    }
}
