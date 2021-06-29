<?php

namespace App\Traits;

use App\Models\Project;
use App\Models\ProjectApplications;
use Illuminate\Support\Facades\DB;

trait ProjectApplicationTraits
{
    public function ownerMarkProjectComplete(Project $project, ProjectApplications $application)
    {
        try {
            return DB::transaction(function () use ($project, $application) {

                $project->update(['completed_on', now()]);
                $application->update(['isCompleted_owner', now()]);
                return true;
            }, 2);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }


    public function taskMasterProjectComplete(ProjectApplications $application)
    {
        try {
            return DB::transaction(function () use ($application) {

                return $application->update(['isCompleted_task_master', now()]);
            }, 2);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
