<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Models\Project;

class ProjectController extends Controller
{
    public function projectAttributes()
    {
        // what is needed to create a project
        $attributes = (new Project())->attributes();
        return ResponseHelper::sendSuccess($attributes, 'successful');
    }

    public function searchProject(Request $request)
    {
        $this->validateSearchRequest($request);

        $projects = Project::query();

        if ($request->model && count($request->model)) {
            $projects->whereIn('model', $request->model);
        }

        if ($request->taskIds && count($request->taskIds)) {
            $projects->whereIn('task_id', $request->taskIds);
        }

        if ($request->subTaskIds && count($request->taskIds)) {
            $projects->whereIn('sub_task_id', $request->subTaskIds);
        }

        if ($request->countryIds && count($request->countryIds)) {
            $projects->whereIn('country_id', $request->countryIds);
        }

        if ($request->regionIds && count($request->regionIds)) {
            $projects->whereIn('region_id', $request->regionIds);
        }

        if ($request->cityIds && count($request->cityIds)) {
            $projects->whereIn('city_id', $request->cityIds);
        }

        if ($request->num_of_taskMaster) {
            $projects->where('num_of_taskMaster', "<=", $request->num_of_taskMaster);
        }

        if ($request->experience && count($request->experience)) {
            $projects->whereIn('experience', $request->experience);
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

        $projects = $projects
            ->with(['task:name,id', 'subtask:name,id', 'owner:name,id', 'photos:url,project_id'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return ResponseHelper::sendSuccess($projects, 'successful');
    }

    public function favouredAProject(Request $request)
    {
        $project = $request->validate(["project_id" => "required|exists:projects,id"]);

        $favoured = $request->user()
            ->likedProjects()->firstOrCreate($project);

        return $favoured ?
            ResponseHelper::sendSuccess([], 'successful') : ResponseHelper::serverError();
    }

    public function favouritesProjects(Request $request)
    {
        $projects = $request->user()
            ->likedProjects()->with(['project.task:name,id', 'project.subtask:name,id', 'project.owner:name,id', 'project.photos:url,project_id']);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->orderBy('updated_at', 'desc')
                ->paginate(10), 'successful') : ResponseHelper::serverError();
    }


    public function index()
    {
        $projects =  Project::query();

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with(['task:name,id', 'subtask:name,id', 'owner:name,id,orders_out,orders_in,ratings', 'country:name,id', 'region:name,id', 'city:name,id', 'photos:url,project_id'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10)) : ResponseHelper::notFound();
    }

    public function usersProject(Request $request)
    {
        $projects =  Project::query()
            ->where('user_id', $request->user()->id);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects
                ->with(['task:name,id', 'subtask:name,id', 'country:name,id', 'region:name,id', 'city:name,id', 'photos:url,project_id'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10)) : ResponseHelper::notFound();
    }

    public function show($projectId)
    {
        $projects =  Project::query()
            ->where('id', $projectId);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects->with(['task:name,id', 'subtask:name,id', 'owner:name,id,orders_out,ratings,orders_in', 'country:name,id', 'region:name,id', 'city:name,id', 'photos:id,url,project_id'])->get()) : ResponseHelper::notFound();
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
        $project =  Project::query()
            ->where('id', $projectId);

        if (!$project->count()) {
            return ResponseHelper::notFound();
        }

        $publisable = $project->first()->isPublishable();
        if ($publisable !== true) {
            return ResponseHelper::badRequest($publisable);
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
            'duration' => 'nullable|string',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'taskIds' => 'nullable|array|min:1',
            'subTaskIds' => 'nullable|array|min:1',
            'countryIds' => 'nullable|array|min:1',
            'regionIds' => 'nullable|array|min:1',
            'cityIds' => 'nullable|array|min:1',
            'model' => 'nullable|array|min:1|max:2',
            'num_of_taskMaster' => 'nullable|integer|min:1',
            'experience' =>  'nullable|array|min:1',

            // 'budget' => 'nullable|numeric|min:10',
            // 'proposed_start_date' => 'nullable|date',
        ]);
    }
}
