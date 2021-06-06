<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition()
    {
        $country = Country::inRandomOrder()->first();
        return [
            'name' => $this->faker->unique()->city(),
            'country_id' => $country,
            'region_id' => $country->regions->first(),
            'latitude' => $this->faker->latitude($min = -90, $max = 90),
            'longitude' => $this->faker->longitude($min = -180, $max = 180),
        ];
    }
}
