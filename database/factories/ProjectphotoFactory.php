<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Projectphoto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectphotoFactory extends Factory
{
    protected $model = Projectphoto::class;

    public function definition()
    {
        return [
            "url" => $this->faker->imageUrl(),
            "project_id" => Project::inRandomOrder()->first()
        ];
    }
}
