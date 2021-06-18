<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Project;
use App\Models\ProjectApplications;
use Illuminate\Http\Request;

class ProjectApplicationController extends Controller
{
    public function accept(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:project_applications'
        ]);

        $projectAssigment = ProjectApplications::query()
            ->where('project_id', $request->project_id)
            ->where('user_id', $request->user()->id)
            ->where('assigned', true);

        if (!$projectAssigment->count()) {
            return ResponseHelper::badRequest("Project has not been assigned to you");
        }

        $projectAssigment = $projectAssigment->where('hasAccepted', false);
        if (!$projectAssigment->count()) {
            return ResponseHelper::badRequest("You have already accepted the project");
        }

        $update = $projectAssigment->update(["hasAccepted" => true]);

        return $update ?
            ResponseHelper::sendSuccess([], "project accepted") : ResponseHelper::serverError();
    }


    public function apply(Request $request)
    {
        $requestData = $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
            'resume' => 'nullable|string',
        ]);
        $project = Project::query()->where('id', $request->project_id)->first();

        $canApply = $request->user()->isProfileCompleted($project);
        if ($canApply !== true) {
            return ResponseHelper::unAuthorised("Complete your profile before applying - {$canApply}");
        }

        return $openForApplications = $project->openForApplications();
        if (!$openForApplications) {
            return ResponseHelper::badRequest("Project has already been assigned");
        }

        $alreadyApplied = ProjectApplications::userAppliedToProject($request->project_id, $request->user()->id);
        if ($alreadyApplied) {
            return ResponseHelper::badRequest("You have already applied to this project");
        }

        $requestData['user_id'] = $request->user()->id;
        $application = ProjectApplications::create($requestData);

        return $application ?
            ResponseHelper::sendSuccess([], 'Application is successful') : ResponseHelper::serverError();
    }

    public function withdraw(Request $request)
    {
        $request->validate(['project_id' => 'required|exists:projects,id']);

        $projectApplication = ProjectApplications::query()
            ->where("user_id", $request->user()->id)
            ->where("project_id", $request->project_id);

        if (!$projectApplication->count()) {
            return ResponseHelper::badRequest("You dont have application on this project");
        }

        $deleteApplication = $projectApplication->delete();
        return $deleteApplication ?
            ResponseHelper::sendSuccess([], "Application withdrawn") : ResponseHelper::serverError();
    }

    public function assignedUsers($projectId)
    {
        $projectAssigments = ProjectApplications::query()
            ->where('project_id', $projectId)
            ->where('assigned', true);

        return $projectAssigments->count() ?
            ResponseHelper::sendSuccess($projectAssigments->with('applicants')->paginate(10)) : ResponseHelper::notFound("Users has not been assigned");
    }

    public function applications($projectId)
    {
        $projectApplications = ProjectApplications::query()
            ->where("project_id", $projectId);

        if (!$projectApplications->count()) {
            return ResponseHelper::notFound("Project doesn't have applications yet");
        }

        return ResponseHelper::sendSuccess($projectApplications->with(['applicants'])->paginate(10));
    }

    public function myApplications(Request $request)
    {
        $myApplications = $request->user()->myApplications();
        if (!$myApplications->count()) {
            return ResponseHelper::notFound("user doesn't have applications yet");
        }

        return $myApplications->with('projects')->paginate(10);
    }
}
