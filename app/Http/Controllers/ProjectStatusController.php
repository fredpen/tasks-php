<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Policies\ProjectPolicy;
use App\Project;
use Illuminate\Support\Facades\Auth;

class ProjectStatusController extends Controller
{
    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->middleware(['auth:api']);
    }

    private function getProject($projectId)
    {
        return $this->project->where('id', $projectId)->first();
    }

    public function updateStatus($projectId, $status)
    {
        $project = $this->getProject($projectId);
        $canEdit = ProjectPolicy::edit(Auth::user(), $project);
        
        if (! $project) {
            return ResponseHelper::notFound();
        } else if (! $canEdit) {
            return ResponseHelper::unAuthorised();
        }
        return $project->updateStatus($status) ? ResponseHelper::sendSuccess([]) : ResponseHelper::serverError();
    }
   
   
}
