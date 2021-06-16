<?php

namespace Database\Seeders;

use App\Models\SubTask;
use App\Models\Tasks;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run()
    {
        Tasks::factory()
            ->count(10)
            ->has(SubTask::factory()->count(10), 'subTasks')
            ->create();
    }
}
