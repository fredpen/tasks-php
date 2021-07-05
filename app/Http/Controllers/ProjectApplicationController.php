<?php

namespace App\Http\Controllers;

use App\Helpers\RatingsHelper;
use App\Helpers\ResponseHelper;
use App\Models\Project;
use App\Models\ProjectApplications;
use App\Traits\ProjectApplicationTraits;
use Illuminate\Http\Request;

class ProjectApplicationController extends Controller
{
    use ProjectApplicationTraits;

    public function rate(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'sometimes|string|min:3|max:200',
        ]);

        $project = Project::query()
            ->where('id', $request->project_id)
            ->where('completed_on', '!=', null);

        if (!$project->count()) {
            return ResponseHelper::badRequest("Project has to be marked completed before they can be rated");
        }

        $project = $project->first();
        $application = $project->applications()
            ->where('hasAccepted', '!=', null)
            ->where('assigned', '!=', null);

        if (!$application->count()) {
            return ResponseHelper::badRequest("Project has to be marked completed before they can be rated");
        }

        $isOwner = $project->isOwner();
        $application = $application->first();
        $isAssignedTaskMaster = $application->isAssignedTaskMaster();

        if (!$isOwner && !$isAssignedTaskMaster) {
            return ResponseHelper::badRequest("You do not own or was assigned to this project");
        }

        $rate = RatingsHelper::rate($application, $isOwner, $request->rating, $request->comment);

        return $rate ? ResponseHelper::sendSuccess([]) :
            ResponseHelper::serverError("Couldnt rate at this time, please try again");
    }

    public function markCompleted(Request $request)
    {
        $request->validate(['project_id' => 'required|integer|exists:projects,id']);

        $project = Project::query()
            ->where('id', $request->project_id)
            ->where('completed_on', null)
            ->where('cancelled_on', null);

        if (!$project->count()) {
            return ResponseHelper::badRequest("project is already completed or canceled");
        }

        $projectApplication = $project->applications()
            ->where('hasAccepted', '!=', null)
            ->where('assigned', '!=', null);

        if (!$projectApplication->count()) {
            return ResponseHelper::badRequest("Project has not been assigned or accepted");
        }

        $project = $project->first();
        $projectApplication = $projectApplication->first();

        if ($project->user_id != $request->user()->id && $projectApplication->user_id != $request->user()->id) {
            return ResponseHelper::badRequest("You do not own or was assigned to this project");
        }

        $isOwner = $project->isOwner();
        if ($project->user_id == $request->user()->id) {
            $markComplete = $this->ownerMarkProjectComplete($project, $projectApplication);
        } else {
            $markComplete = $this->taskMasterMarksProjectComplete($projectApplication);
        }

        return $markComplete === true ? ResponseHelper::sendSuccess([]) :
            ResponseHelper::serverError($markComplete);
    }

    public function accept(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:project_applications'
        ]);

        $projectAssigment = ProjectApplications::query()
            ->where('project_id', $request->project_id)
            ->where('user_id', $request->user()->id)
            ->where('assigned', '!=', null);

        if (!$projectAssigment->count()) {
            return ResponseHelper::badRequest("Project has not been assigned to you");
        }

        $projectAssigment = $projectAssigment->where('hasAccepted', null);
        if (!$projectAssigment->count()) {
            return ResponseHelper::badRequest("You have already accepted the project");
        }

        $update = $projectAssigment->update(["hasAccepted" => now()]);

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

        $openForApplications = $project->openForApplications();
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
            ResponseHelper::sendSuccess([], 'Application is successfull') : ResponseHelper::serverError();
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
            ->where('assigned', '!=', null);

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
