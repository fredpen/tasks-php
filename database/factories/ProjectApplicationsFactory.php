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
        $decider = rand(0, 1) == 0 ? true : false;
        return [
            'project_id' =>  Project::inRandomOrder()->first(),
            'user_id' =>  User::inRandomOrder()->first(),
            'resume' => $this->faker->sentence,
            'assigned' => $decider ? $this->faker->dateTimeBetween($startDate = '-3 years', $endDate = 'now', $timezone = null) : null,
            'hasAccepted' => $decider ? $this->faker->dateTimeBetween($startDate = '-2 years', $endDate = 'now', $timezone = null) : null,
            'isCompleted_owner' => $decider ? $this->faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null) : null,
            'isCompleted_task_master' => $decider ? $this->faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null) : null,
            'taskMaster_rating' =>  $decider ? $this->faker->numberBetween($min = 1, $max = 5) : null,
            'owner_rating' => $decider ?  $this->faker->numberBetween($min = 1, $max = 5) : null,
            'taskMaster_comment' => $decider ? $this->faker->paragraph($nbSentences = 3, $variableNbSentences = true) : null,
            'owner_comment' => $decider ? $this->faker->paragraph($nbSentences = 3, $variableNbSentences = true) : null,
        ];
    }
}
