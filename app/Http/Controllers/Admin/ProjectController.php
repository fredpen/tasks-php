<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Project;
use App\Http\Controllers\Controller;


class ProjectController extends Controller
{
    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function fetchProjectWithStatus($status)
    {
        $projects =  $this->project->where('status', $status);
        return $projects->count() ? ResponseHelper::sendSuccess($projects->paginate(20)) : ResponseHelper::notFound();
    }
   
    public function fetchProjectWithModel($model)
    {
        $projects =  $this->project->where('model', $model);
        return $projects->count() ? ResponseHelper::sendSuccess($projects->paginate(20)) : ResponseHelper::notFound();
    }
   
    public function usersProject($userId)
    {
        $projects =  $this->project->where('user_id', $userId);
        return $projects->count() ? ResponseHelper::sendSuccess($projects->paginate(20)) : ResponseHelper::notFound();
    }

   

}
