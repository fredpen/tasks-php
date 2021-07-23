<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectApplications;
use Illuminate\Http\Request;

class ProjectApplicationController extends Controller
{
    public function assign(Request $request)
    {
        $request->validate([
            'project_id' => 'bail|required|exists:project_applications',
            'user_id' => 'bail|required|exists:project_applications',
        ]);

        $project = Project::query()->where('id', $request->project_id)->first();

        $openForApplications = $project->openForApplications();
        if (!$openForApplications) {
            return ResponseHelper::badRequest("Project has already been assigned");
        }

        $isPaidFor = $project->isPaidFor();
        if (!$isPaidFor) {
            return ResponseHelper::badRequest("Project has not been paid for");
        }

        $projectApplication = ProjectApplications::query()
            ->where('project_id', $request->project_id)
            ->where('user_id', $request->user_id)
            ->where('assigned', null);

        if (!$projectApplication->count()) {
            return ResponseHelper::badRequest("Project has already been assigned to you");
        }

        $time = now();
        $update = $projectApplication->update(['assigned' => $time]);
        $update = $project->update(['assigned_on' => $time]);

        return $update ?
            ResponseHelper::sendSuccess([], 'Project assigned') : ResponseHelper::serverError();
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'project_id' => 'bail|required|exists:project_applications',
            'user_id' => 'bail|required|exists:project_applications',
        ]);

        $projectApplication = ProjectApplications::query()
            ->where('project_id', $request->project_id)
            ->where('user_id', $request->user_id)
            ->where('assigned', true);

        if (!$projectApplication->count()) {
            return ResponseHelper::badRequest("User is not assigned to the project");
        }

        $update = $projectApplication->update(['assigned' => null]);

        return $update ?
            ResponseHelper::sendSuccess([], 'Project unassigned') : ResponseHelper::serverError();
    }
}
