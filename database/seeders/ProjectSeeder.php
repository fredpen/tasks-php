<?php

namespace Database\Seeders;

use App\Models\FavouredProject;
use App\Models\Project;
use App\Models\ProjectApplications;
use App\Models\Projectphoto;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{

    public function run()
    {
        Project::factory()
            ->has(Projectphoto::factory()->count(3), 'photos')
            ->has(FavouredProject::factory()->count(10), 'photos')
            ->has(ProjectApplications::factory()->count(10), 'applications')
            ->count(50)
            ->create();
    }
}
