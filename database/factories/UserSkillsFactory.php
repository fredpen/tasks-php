<?php

namespace Database\Factories;

use App\Models\SubTask;
use App\Models\User;
use App\Models\UserSkills;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSkillsFactory extends Factory
{

    protected $model = UserSkills::class;


    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first(),
            'sub_task_id' => SubTask::inRandomOrder()->first(),
            'isSkilllverfiied' => $this->faker->randomElement([true, false]),
        ];
    }
}
