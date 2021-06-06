<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $projects =  Project::query();

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects->with(['task:name,id', 'subtask:name,id', 'owner:name,id,orders_out,orders_in,ratings', 'country:name,id', 'region:name,id', 'city:name,id'])->paginate(10)) : ResponseHelper::notFound();
    }

    public function usersProject(Request $request)
    {
        $projects =  Project::query()
            ->where('user_id', $request->user()->id)
            ->where('deleted_on', null);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects->with(['task:name,id', 'subtask:name,id', 'country:name,id', 'region:name,id', 'city:name,id'])->paginate(10)) : ResponseHelper::notFound();
    }

    public function show($projectId)
    {
        $projects =  Project::query()
            ->where('deleted_on', null)
            ->where('id', $projectId);

        return $projects->count() ?
            ResponseHelper::sendSuccess($projects->with(['task:name,id', 'subtask:name,id', 'owner:name,id,orders_out,ratings,orders_in', 'country:name,id', 'region:name,id', 'city:name,id', 'photos:url,project_id'])->get()) : ResponseHelper::notFound();
    }

    public function store(Request $request)
    {
        $data = $this->validateProjectRequest($request);
        $data['user_id'] = $request->user()->id;
        $project =  Project::create($data);

        return $project ? ResponseHelper::sendSuccess([]) : ResponseHelper::serverError();
        // $project->owner->notify((new projectCreated));
    }


    public function publish($projectId)
    {
        $project =  Project::query()
            ->where('deleted_on', null)
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
        $this->validateProjectRequest($request);

        $project =  Project::query()
            ->where('deleted_on', null)
            ->where('id', $request->project_id);

        if (!$project->count()) {
            return ResponseHelper::notFound();
        }

        $update = $project->update($request->except(['id', 'user_id', 'isActive', 'status', 'posted_on', 'started_on', 'completed_on', 'cancelled_on', 'deleted_on', 'project_id']));

        return $update ?
            ResponseHelper::sendSuccess([], 'update successful') : ResponseHelper::serverError();

        // $project->owner->notify((new ProjectPosted)->delay(10)->onQueue('notifs'));
    }

    public function delete(Request $request)
    {
        $project =  Project::query()
            ->where('deleted_on', null)
            ->where('id', $request->project_id);

        if (!$project->count()) {
            return ResponseHelper::notFound();
        }

        if ($project->first()->posted_on == null) {
            return $project->delete() ?
                ResponseHelper::sendSuccess([]) : ResponseHelper::serverError();
        }

        return $project->update(['deleted_on' => now()]) ?
            ResponseHelper::sendSuccess([]) : ResponseHelper::serverError();
        // $project->owner->notify((new ProjectCancelled)->delay(10));
    }

    private function validateProjectRequest($request)
    {
        return $request->validate([
            'task_id' => 'integer|exists:tasks,id',
            'sub_task_id' => 'integer|exists:sub_tasks,id',
            'country_id' => 'integer|exists:countries,id',
            'region_id' => 'integer|exists:regions,id',
            'city_id' => 'integer|exists:cities,id',

            'model' => 'integer|min:1',
            'num_of_taskMaster' => 'integer|min:1',
            'budget' => 'number|min:10',
            'experience' => 'integer',
            'proposed_start_date' => 'date',
            'description' => 'string',
            'title' => 'string',
            'duration' => 'string',
            'address' => 'string',
        ]);
    }
}
