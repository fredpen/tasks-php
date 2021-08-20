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

        if ($decider) {
            return [
                'project_id' =>  Project::where('proposed_start_date', '!=', null)
                    ->where('posted_on', '!=', null)
                    ->where('assigned_on', '!=', null)
                    ->where('started_on', '!=', null)
                    ->where('cancelled_on',  null)
                    ->inRandomOrder()
                    ->first(),
                'user_id' =>  User::inRandomOrder()->first(),
                'resume' => $this->faker->sentence,
                'assigned' => $this->faker->dateTimeBetween($startDate = '-3 years', $endDate = 'now', $timezone = null),
                'hasAccepted' => $this->faker->dateTimeBetween($startDate = '-2 years', $endDate = 'now', $timezone = null),
                'isCompleted_owner' => $this->faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null),
                'isCompleted_task_master' => $this->faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null),
                'taskMaster_rating' => $this->faker->numberBetween($min = 1, $max = 5),
                'owner_rating' => $this->faker->numberBetween($min = 1, $max = 5),
                'taskMaster_comment' => $this->faker->paragraph($nbSentences = 3, $variableNbSentences = true),
                'owner_comment' => $this->faker->paragraph($nbSentences = 3, $variableNbSentences = true),
            ];
        }

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
