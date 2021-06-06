<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'region_id' => $this->faker->numberBetween($min = 1, $max = 10),
            'country_id' => $this->faker->numberBetween($min = 1, $max = 10),
            'latitude' => $this->faker->ean8,
            'longitude' => $this->faker->ean8,
        ];
    }
}
