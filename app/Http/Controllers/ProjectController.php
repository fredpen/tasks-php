<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Models\Project;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Config;

class ProjectController extends Controller
{
    private $Limit = 20;

    public function relatedProjects(Request $request)
    {
        $project = Project::find($request->project_id);
        if (!$project) {
            return ResponseHelper::notFound('Invalid project Id');
        }

        $projects = Project::where('id', '!=', $project->id);

        $projects = $projects->where(function ($query) use ($project) {
            $query->where('task_id', $project->id)
                ->orWhere('region_id', $project->region_id)
                ->orWhere('model', $project->model);
        });

        if (!$projects->count()) {
            return ResponseHelper::notFound("Query returns empty");
        }

        $attributes = Config::get('protectedWith.project');
        $projects = $projects
            ->with($attributes)
            ->orderBy('updated_at', 'desc')
            ->inRandomOrder()
            ->take(4)
            ->get();

        return ResponseHelper::sendSuccess($projects, 'successful');
    }

    public function appliableProjects()
    {
        $projects =  Project::query()->where('cancelled_on', null)
            ->where('deleted_at', null)
            ->where('assigned_on', null)
            ->where('posted_on', '!=', null);

        $attributes = Config::get('protectedWith.project');

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->Limit)) : ResponseHelper::notFound();
    }

    public function projectAttributes()
    {
        // what is needed to create a project
        $attributes = (new Project())->attributes();
        return ResponseHelper::sendSuccess($attributes, 'successful');
    }

    public function searchProject(Request $request)
    {
        $data = $this->validateSearchRequest($request);

        $projects = Project::query();
        $searchKeys = array_keys($data);
        $whereInParams = Config::get('constants.whereInSearchQuery');

        foreach ($searchKeys as $searchKey) {
            if (in_array($searchKey, $whereInParams)) {
                $projects->localWhereIn($searchKey, $request->$searchKey);
            }
        }

        if ($request->num_of_taskMaster) {
            $projects->where('num_of_taskMaster', "<=", $request->num_of_taskMaster);
        }

        if ($request->description) {
            $description = $request->description;
            $projects = $projects->where(function ($query) use ($description) {
                $query->orWhere('description', "like", "%{$description}%")
                    ->orWhere('address', "like", "%{$description}%")
                    ->orWhere('title', "like", "%{$description}%");
            });
        }

        if (!$projects->count()) {
            return ResponseHelper::notFound("Query returns empty");
        }

        $attributes = Config::get('protectedWith.project');
        $projects = $projects
            ->with($attributes)
            ->orderBy('updated_at', 'desc')
            ->paginate($this->Limit);

        return ResponseHelper::sendSuccess($projects, 'successful');
    }

    public function unFavouredAProject(Request $request)
    {
        $project = $request->validate(["project_id" => "required|exists:projects,id"]);

        $likedProject = $request->user()
            ->likedProjects()
            ->where('project_id', $request->project_id);

        if (!$likedProject->count()) {
            return ResponseHelper::badRequest("You have not favoured this project before");
        }

        return  $likedProject->delete() ?
            ResponseHelper::sendSuccess([], 'successful') : ResponseHelper::serverError();
    }

    public function favouredAProject(Request $request)
    {
        $project = $request->validate(["project_id" => "required|exists:projects,id"]);

        $likedProject = $request->user()
            ->likedProjects()
            ->where('project_id', $request->project_id);

        if ($likedProject->count()) {
            return ResponseHelper::badRequest("You have already favoured this project");
        }

        $favoured = $request->user()
            ->likedProjects()
            ->create($project);

        return $favoured ?
            ResponseHelper::sendSuccess([], 'successful') : ResponseHelper::serverError();
    }

    public function index()
    {
        $projects =  Project::query();
        $attributes = Config::get('protectedWith.project');

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->Limit)) : ResponseHelper::notFound();
    }

    public function activeProjects()
    {
        $projects =  Project::query()->where('cancelled_on', null);
        $attributes = Config::get('protectedWith.project');

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->orderBy('updated_at', 'desc')
                ->paginate($this->Limit)) : ResponseHelper::notFound();
    }


    public function show($projectId)
    {
        $projects =  Project::query()->where('id', $projectId);
        $attributes = Config::get('protectedWith.project');

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with($attributes)
                ->get()) : ResponseHelper::notFound();
    }

    public function store(Request $request)
    {
        $request->validate(['budget' => 'required|numeric|min:10']);
        $data = $this->validateProjectRequest($request);
        $create = $request->user()->projects()->create($data);

        return $create ? ResponseHelper::sendSuccess([]) : ResponseHelper::serverError();
        // $project->owner->notify((new projectCreated));
    }

    public function publish($projectId)
    {
        $project =  Project::where('id', $projectId);

        if (!$project->count()) {
            return ResponseHelper::notFound();
        }

        $project = $project->first();
        if ($project->posted_on) {
            return ResponseHelper::badRequest("Project has already been publish");
        }

        $errorMessage = $project->isPublishable();
        if ($errorMessage !== true) {
            return ResponseHelper::badRequest($errorMessage);
        }

        $update = $project->update(['posted_on' => now()]);
        return $update ?
            ResponseHelper::sendSuccess([], 'Project is now live') : ResponseHelper::serverError();

        // $project->owner->notify((new ProjectPosted)->delay(10)->onQueue('notifs'));
    }

    public function update(Request $request)
    {
        $request->validate(['project_id' => 'required|exists:projects,id']);
        $this->validateProjectRequest($request);

        $project =  Project::query()->where('id', $request->project_id);
        if (!$project->count()) {
            return ResponseHelper::notFound("Invalid Project ID");
        }

        $update = $project->update($request->except(['id', 'user_id', 'isActive', 'status', 'posted_on', 'started_on', 'completed_on', 'cancelled_on', 'deleted_on', 'project_id']));

        return $update ?
            ResponseHelper::sendSuccess([], 'update successful') : ResponseHelper::serverError();

        // $project->owner->notify((new ProjectPosted)->delay(10)->onQueue('notifs'));
    }

    public function cancel(Request $request)
    {
        $request->validate(['project_id' => 'required|integer|exists:projects,id']);

        $project =  Project::query()->where('id', $request->project_id);
        if (!$project->count()) {
            return ResponseHelper::notFound();
        }

        $project = $project->first();
        if ($project->cancelled_on) {
            return ResponseHelper::badRequest("Project has already been cancelled");
        }

        $errorMessage = $project->isCancellable();
        if ($errorMessage !== true) {
            return ResponseHelper::badRequest($errorMessage);
        }

        return $project->update(['cancelled_on' => now()]) ?
            ResponseHelper::sendSuccess([]) : ResponseHelper::serverError();
        // $project->owner->notify((new ProjectCancelled)->delay(10));
    }

    public function delete(Request $request)
    {
        $request->validate(['project_id' => 'required|integer|exists:projects,id']);

        $project =  Project::query()->where('id', $request->project_id);
        if (!$project->count()) {
            return ResponseHelper::notFound();
        }

        $errorMessage = $project->first()->isDeletable();
        if ($errorMessage !== true) {
            return ResponseHelper::badRequest($errorMessage);
        }

        return $project->delete() ?
            ResponseHelper::sendSuccess([]) : ResponseHelper::serverError();
        // $project->owner->notify((new ProjectCancelled)->delay(10));
    }

    private function validateProjectRequest($request)
    {
        return $request->validate([
            'task_id' => 'nullable|integer|exists:tasks,id',
            'sub_task_id' => 'nullable|integer|exists:sub_tasks,id',
            'country_id' => 'nullable|integer|exists:countries,id',
            'region_id' => 'nullable|integer|exists:regions,id',
            'city_id' => 'nullable|integer|exists:cities,id',


            'model' => 'nullable|integer|min:1',
            'num_of_taskMaster' => 'nullable|integer|min:1',
            'budget' => 'nullable|numeric|min:10',
            'experience' => 'nullable|integer',
            'proposed_start_date' => 'nullable|date',
            'description' => 'nullable|string',
            'title' => 'nullable|string',
            'duration' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
    }

    private function validateSearchRequest(Request $request)
    {
        return $request->validate([
            'num_of_taskMaster' => 'sometimes|integer|min:1',
            'address' => 'sometimes|string',
            'description' => 'sometimes|string',
            'title' => 'sometimes|string',

            'task_id' => 'sometimes|array|min:1',
            'sub_task_id' => 'sometimes|array|min:1',
            'country_id' => 'sometimes|array|min:1',
            'region_id' => 'sometimes|array|min:1',
            'city_id' => 'sometimes|array|min:1',
            'model' => 'sometimes|array|min:1|max:2',
            'experience' =>  'sometimes|array|min:1',
            // 'budget' => 'nullable|numeric|min:10',
            // 'proposed_start_date' => 'nullable|date',
        ]);
    }

    ///usrers

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

    public function usersDraftProject(Request $request)
    {
        $attributes = Config::get('protectedWith.project');
        $projects = $request->user()
            ->projects()
            ->where('posted_on', null);

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

    public function favouriteProjectsIds(Request $request)
    {
        $projects = $request->user()->likedProjects();

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->distinct()
                ->pluck('project_id')) : ResponseHelper::notFound();
    }
}
