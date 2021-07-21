<?php

namespace App\Traits;

use App\Models\Project;
use App\Models\ProjectApplications;
use Illuminate\Support\Facades\DB;

trait ProjectApplicationTraits
{
    public function completeProject(Project $project, ProjectApplications $application, bool $isOwner)
    {
        try {
            return DB::transaction(function () use ($project, $application, $isOwner) {
                $time = now();
                $completedColumn = $isOwner ? "isCompleted_owner" : "isCompleted_task_master";
                // $projectCompletedTime = $isOwner ? $time : null;

                $application->update([$completedColumn => $time]);
                $project->update(['completed_on' => $time]);
                return true;
            }, 2);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }


    public function startProject(Project $project, ProjectApplications $application)
    {
        try {
            return DB::transaction(function () use ($project, $application) {

                $time = now();
                $application->update(["hasAccepted" => $time]);
                $project->update(["started_on" => $time]);
                 
                return true;
            }, 2);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
