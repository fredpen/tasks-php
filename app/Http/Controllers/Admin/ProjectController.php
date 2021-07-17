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

    public function drafts()
    {
        $attributes = Config::get('protectedWith.project');
        $projects = Project::query()->where('posted_on', null);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->limit)) : ResponseHelper::notFound();
    }

    public function published()
    {
        $attributes = Config::get('protectedWith.project');
        $projects = Project::query()->where('posted_on', '!=', null);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->limit)) : ResponseHelper::notFound();
    }

    public function started()
    {
        $attributes = Config::get('protectedWith.project');
        $projects = Project::query()->where('started_on', '!=', null);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->limit)) : ResponseHelper::notFound();
    }

    public function completed()
    {
        $attributes = Config::get('protectedWith.project');
        $projects = Project::query()->where('completed_on', '!=', null);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->limit)) : ResponseHelper::notFound();
    }

    public function cancelled()
    {
        $attributes = Config::get('protectedWith.project');
        $projects = Project::query()->where('cancelled_on', '!=', null);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->limit)) : ResponseHelper::notFound();
    }

    public function deleted()
    {
        $attributes = Config::get('protectedWith.project');
        $projects = Project::query()->where('deleted_at', '!=', null);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->limit)) : ResponseHelper::notFound();
    }

    public function usersProject(Request $request)
    {
        $projects = Project::query()->where('user_id', $request->user_id);
        $attributes = Config::get('protectedWith.project');

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->limit)) : ResponseHelper::notFound();
    } 
}
