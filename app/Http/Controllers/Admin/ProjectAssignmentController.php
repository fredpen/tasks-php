<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\ProjectAssignedUser;
use App\User;
use App\Notifications\ProjectAssignment;
use App\Notifications\TaskWithdrawal;

class ProjectAssignmentController extends Controller
{
    protected $projectAssignedUser;

    public function __construct(ProjectAssignedUser $projectAssignedUser)
    {
        $this->projectAssignedUser = $projectAssignedUser;
    }

    public function assign($projectId, $user_id)
    {
        $request = ['project_id' => $projectId, 'user_id' => $user_id];
        $projectassignedUser = $this->projectAssignedUser->firstOrCreate($request);
        $user = User::findOrFail($user_id);
        $user->notify(new ProjectAssignment($projectId));
        return $projectassignedUser ? ResponseHelper::sendSuccess([]) : ResponseHelper::serverError();
    }

    public function withdrawAssignment($projectId, $user_id)
    {
        $projectassignedUser = $this->projectAssignedUser->where(['user_id' => $user_id, 'project_id' => $projectId])->first();
        if (! $projectassignedUser) {
            return ResponseHelper::notFound();
        }
        
        $delete = $projectassignedUser->delete();
        $user = User::findOrFail($user_id);
        $user->notify(new TaskWithdrawal($projectId));
        return $delete ? ResponseHelper::sendSuccess([]) : ResponseHelper::serverError();
    }

    

}
