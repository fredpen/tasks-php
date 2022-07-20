<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use App\Helpers\ResponseHelper;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectSearchController extends Controller
{
    public function all(Request $request)
    {
        $projects =  Project::query();

        if ($request->filled('keyword')) {
            $projects = $projects->where(function ($query) use ($request) {
                $query->where("address", "like", "%$request->keyword%")
                    ->orWhere("title", "like", "%$request->keyword%")
                    ->orWhere("description", "like", "%$request->keyword%");
            });
        }

        if ($request->filled('sub_task_id')) {
            $projects = $projects->where("sub_task_id", $request->sub_task_id);
        }

        if ($request->filled('task_id')) {
            $projects = $projects->where("task_id", $request->task_id);
        }

        if ($request->filled('country_id')) {
            $projects = $projects->where("country_id", $request->country_id);
        }

        if ($request->filled('region_id')) {
            $projects = $projects->where("region_id", $request->region_id);
        }

        if ($request->filled('city_id')) {
            $projects = $projects->where("city_id", $request->city_id);
        }

        if ($request->filled('model')) {
            $projects = $projects->where("model", $request->model);
        }

        if ($request->filled('min_budget')) {
            $projects = $projects->where("budget", ">=", $request->min_budget);
        }

        if ($request->filled('hasPaid')) {
            $projects = $projects->where("hasPaid", $request->hasPaid);
        }

        return $this->filter($projects);
    }

    public function popular()
    {
        $projects =  Project::withCount('applications');
        return $this->filter($projects, "applications_count");
    }

    public function appliableProjects()
    {
        $projects =  (new Project)->appliable();
        return $this->filter($projects);
    }

    public function activeProjects()
    {
        $projects =  Project::query()
            ->where('cancelled_on', null)
            ->where('deleted_at', null);
        return $this->filter($projects);
    }


    private function filter($query, $order = "updated_at")
    {
        $attributes = Config::get('protectedWith.project');

        $projects = $query->with($attributes)
            ->latest($order)
            ->paginate(request()->per_page ?? $this->limit);

        return ResponseHelper::sendSuccess($projects->count() ? $projects : []);
    }
}
