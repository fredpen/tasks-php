<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Projectphoto;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{

    public function run()
    {
        Project::factory()
        ->has(Projectphoto::factory(), 'photos')
        ->count(50)
        ->create();
    }
}
