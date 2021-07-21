<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectApplications;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectApplicationsFactory extends Factory
{

    protected $model = ProjectApplications::class;

    public function definition()
    {

        return [
            'project_id' =>  Project::inRandomOrder()->first(),
            'user_id' =>  User::inRandomOrder()->first(),
            'resume' => $this->faker->sentence,
        ];
    }
}
