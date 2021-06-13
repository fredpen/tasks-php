<?php

namespace Database\Factories;

use App\Models\FavouredProject;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavouredProjectFactory extends Factory
{

    protected $model = FavouredProject::class;

    public function definition()
    {
        return [
            "project_id" => Project::inRandomOrder()->first(),
            "user_id" => User::inRandomOrder()->first(),
            "created_at" => now(),
        ];
    }
}
