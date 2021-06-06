<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition()
    {
        return [
            'name' => $this-> faker->unique()->state(),
            'code' => $this->faker->ean8,
            'country_id' => $this->faker->numberBetween($min = 1, $max = 10)
        ];
    }
}
