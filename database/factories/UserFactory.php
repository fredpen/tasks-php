<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $roles = array_keys(Config::get('constants.roles'));
        $country = Country::inRandomOrder()->first();
        $region = $country->regions->first();
        $city = $region->cities->first();
        $valid = rand(0, 1) == 1;

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'isActive' => 0,
            'role_id' => $this->faker->randomElement($roles),
            'phone_number' =>  $this->faker->phoneNumber,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            "title" => $valid ?  $this->faker->sentence(10) : null,
            "bio" => $valid ?  $this->faker->paragraph(10) : null,
            "linkedln" => $valid ?  $this->faker->sentence(10) : null,
            "country_id" =>$valid ?  $country : null,
            "region_id" =>$valid ?  $region : null,
            "city_id" =>$valid ?  $city : null,
            "address" => $valid ?  $this->faker->address : null,
            "identification" => $valid ? $this->faker->imageUrl() : null,
            "avatar" => $valid ? $this->faker->imageUrl() : null,
        ];
    }

    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
