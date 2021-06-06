<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{

    public function run()
    {
        User::factory()
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
