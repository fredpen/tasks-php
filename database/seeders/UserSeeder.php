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
            ->create(
                [
                    'name' => "Admin Admin",
                    'email' => "fred@gmail.com",
                    "isActive" => 1,
                    "role_id" => 0
                ]
            );
    }
}
