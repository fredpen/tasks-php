<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ProjectController extends Controller
{
    public function deepSearch(Request $request)
    {
        $data = $this->validateSearchRequest($request);

        $projects = Project::query();
        $searchKeys = array_keys($data);
        foreach ($searchKeys as $searchKey) {
            $whereInParams = Config::get('constants.whereInSearchQuery');
            if (in_array($searchKey, $whereInParams)) {
                $projects->localWhereIn($searchKey, $request->$searchKey);
            }
        }

        if ($request->searchTerm) {
            $searchTerm = $request->searchTerm;
            $projects = $projects->where(function ($query) use ($searchTerm) {
                $query->orWhere('description', "like", "%{$searchTerm}%")
                    ->orWhere('address', "like", "%{$searchTerm}%")
                    ->orWhere('title', "like", "%{$searchTerm}%");
            });
        }

        if (!$projects->count()) {
            return ResponseHelper::sendSuccess([], "Query returns empty");
        }

        if ($request->project_status) {
            $projectStatus = $request->project_status;
            $projects = $this->{$projectStatus}($projects);
        }

        $attributes = Config::get('protectedWith.project');
        $projects = $projects
            ->with($attributes)
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return ResponseHelper::sendSuccess($projects, 'successful');
    }



    public function search(string $searchTerm)
    {
        $lookUp = collect(["drafts", "published", "started", "completed", "cancelled", "deleted"]);

        if (!$lookUp->contains($searchTerm)) {
            return ResponseHelper::invalidRoute("Invalid identifier '{$searchTerm}'");
        }

        $projects = $this->{$searchTerm}();

        if (!$projects->count()) {
            return ResponseHelper::notFound("Query returns empty");
        }

        return ResponseHelper::sendSuccess(
            $projects->with(Config::get('protectedWith.project'))
                ->latest()
                ->paginate($this->limit)
        );
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

    private function drafts($projects = null)
    {
        $projects = $projects ? $projects : new Project();

        return $projects->where('posted_on', null)
            ->where("cancelled_on", null)
            ->where("deleted_at", null);
    }

    private function published($projects = null)
    {
        $projects = $projects ? $projects : new Project();

        return Project::where('posted_on', '!=', null)
            ->where("cancelled_on", null)
            ->where("deleted_at", null);
    }

    private function started($projects = null)
    {
        $projects = $projects ? $projects : new Project();

        return $projects->where('started_on', '!=', null)
            ->where("cancelled_on", null)
            ->where("deleted_at", null);
    }

    private function completed($projects = null)
    {
        $projects = $projects ? $projects : new Project();

        return $projects->where('completed_on', '!=', null)
            ->where("cancelled_on", null)
            ->where("deleted_at", null);
    }

    private function cancelled($projects = null)
    {
        $projects = $projects ? $projects : new Project();

        return $projects->where('cancelled_on', '!=', null);
    }

    private function deleted($projects = null)
    {
        $projects = $projects ? $projects : new Project();

        return $projects->where('deleted_at', '!=', null);
    }

    private function validateSearchRequest(Request $request)
    {
        return $request->validate([
            'searchTerm' => 'sometimes|string|min:3',
            'task_id' => 'sometimes|array|min:1',
            'sub_task_id' => 'sometimes|array|min:1',
            'country_id' => 'sometimes|array|min:1',
            'region_id' => 'sometimes|array|min:1',
            'city_id' => 'sometimes|array|min:1',
            'model' => 'sometimes|array|min:1|max:2',
            "project_status" => 'sometimes|in:"drafts", "published", "started", "completed", "cancelled", "deleted", "all"',
        ]);
    }
}
