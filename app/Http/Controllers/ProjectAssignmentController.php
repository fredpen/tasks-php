<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\ProjectAssignedUser;
use Illuminate\Support\Facades\Auth;

class ProjectAssignmentController extends Controller
{
    protected $projectAssignedUser;

    public function __construct(ProjectAssignedUser $projectAssignedUser)
    {
        $this->projectAssignedUser = $projectAssignedUser;
        $this->middleware(['auth:api', 'verifyEmail', 'isActive'])->only(['accept']);
    }

    public function projectAssignedUser($projectId)
    {
        $projectassignedUser = $this->projectAssignedUser->where('project_id', $projectId)->get();
        return $projectassignedUser ? ResponseHelper::sendSuccess($projectassignedUser) : ResponseHelper::serverError();
    }

    public function accept($projectId) 
    {
        $projectassignedUser = $this->projectAssignedUser->where([ 'project_id' => $projectId, 'user_id' => Auth::id(), 'status' => 'assigned'])->first();
        if (! $projectassignedUser) {
            return ResponseHelper::notFound();
        }
        $accept = $projectassignedUser->update(['status' => 'accept']);
        return $accept ? ResponseHelper::sendSuccess($accept) : ResponseHelper::serverError();
    }

}
