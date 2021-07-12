<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ProjectController extends Controller
{
    private $limit = 20;

    public function drafts(Request $request)
    {
        $attributes = Config::get('protectedWith.project');
        $projects = Project::query()->where('posted_on', null);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->limit)) : ResponseHelper::notFound();
    }

    public function published(Request $request)
    {
        $attributes = Config::get('protectedWith.project');
        $projects = Project::query()->where('posted_on', '!=', null);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->limit)) : ResponseHelper::notFound();
    }

    public function usersProject(Request $request)
    {
        $projects = $request->user()->projects();
        $attributes = Config::get('protectedWith.project');

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->Limit)) : ResponseHelper::notFound();
    }



    public function usersCancelProject(Request $request)
    {
        $attributes = Config::get('protectedWith.project');
        $projects = $request->user()
            ->projects()
            ->where('cancelled_on', '!=', null);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->Limit)) : ResponseHelper::notFound();
    }

    public function usersCompletedProject(Request $request)
    {
        $attributes = Config::get('protectedWith.project');
        $projects = $request->user()
            ->projects()
            ->where('completed_on', '!=', null);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->Limit)) : ResponseHelper::notFound();
    }


    public function usersRunningProject(Request $request)
    {
        $attributes = Config::get('protectedWith.project');
        $projects = $request->user()
            ->projects()
            ->where('started_on', '!=', null)
            ->where('cancelled_on', null)
            ->where('completed_on', null);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->Limit)) : ResponseHelper::notFound();
    }


    public function favouritesProjects(Request $request)
    {
        $projects = $request->user()->likedProjects();
        $attributes = Config::get('protectedWith.favouredProject');

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->Limit)) : ResponseHelper::notFound();
    }
}
