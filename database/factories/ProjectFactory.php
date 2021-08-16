<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Project;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Config;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition()
    {
        $models = array_keys(Config::get('constants.projectModels'));
        $projectStatus = array_keys(Config::get('constants.projectStatus'));
        $projectDuration = array_keys(Config::get('constants.projectDuration'));
        $projectExpertise = array_keys(Config::get('constants.projectExpertise'));
        $task = Tasks::inRandomOrder()->first();
        $country = Country::inRandomOrder()->first();
        $region = $country->regions->first();
        $timeSlots = [now(), null, now()];

        return [
            'user_id' => User::inRandomOrder()->first(),
            'model' => $this->faker->randomElement($models),
            'num_of_taskMaster' => $this->faker->numberBetween(1, 10),
            'budget' => $this->faker->randomNumber(5),
            'isActive' => $this->faker->randomElement([0, 1]),
            'status' => $this->faker->randomElement($projectStatus),
            'amount_paid' => $this->faker->randomNumber(5),
            'experience' => $this->faker->randomElement($projectExpertise),
            'posted_on' => $this->faker->randomElement($timeSlots),
            'started_on' => $this->faker->randomElement($timeSlots),
            'completed_on' => $this->faker->randomElement($timeSlots),
            'cancelled_on' => $this->faker->randomElement($timeSlots),
            'description' =>  $this->faker->paragraph(2),
            'title' =>  $this->faker->sentence(),
            'task_id' =>  $task,
            'sub_task_id' =>  $task->subTasks->first(),
            'country_id' =>  $country,
            'region_id' =>  $region,
            'city_id' =>  $region->cities->first(),
            'address' =>  $this->faker->unique()->address(),
            'duration' =>   $this->faker->randomElement($projectDuration),
        ];
    }
}
