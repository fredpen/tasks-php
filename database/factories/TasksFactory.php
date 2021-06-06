<?php

namespace Database\Factories;

use App\Models\Tasks;
use Illuminate\Database\Eloquent\Factories\Factory;

class TasksFactory extends Factory
{
    protected $model = Tasks::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->jobTitle(),
        ];
    }
}

