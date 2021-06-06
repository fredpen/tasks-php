<?php

namespace App\Http\Controllers;

use App\Models\SubTask;
use App\Models\Tasks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Country;
use App\Models\Region;
use App\Models\City;
use App\Helpers\ResponseHelper;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    protected $project;

    public function __construct(Project $project)
    {
        $this->middleware(['auth:api', 'verifyEmail', 'isActive'])->except(['show', 'index']);
        $this->project = $project;
    }

    public function index()
    {
        $projects =  $this->project;
        return $projects->count() ?
            ResponseHelper::sendSuccess($projects->paginate(20)) : ResponseHelper::notFound();
    }

    public function usersProject()
    {
        $projects =  $this->project->where(['user_id' => Auth::id(), 'deleted_on' => null]);
        return $projects->count() ?
            ResponseHelper::sendSuccess($projects->paginate(20)) : ResponseHelper::notFound();
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateCreateRequest($request->all());
        if ($validatedData->fails()) {
            return ResponseHelper::badRequest($validatedData->errors()->first());
        }
        $createRequest = $request->all();
        $createRequest['user_id'] = Auth::id();

        $project =  $this->project->create($createRequest);
        return $project ? ResponseHelper::sendSuccess($project) : ResponseHelper::serverError();
        // $project->owner->notify((new projectCreated));
    }

    public function show($projectId)
    {
        $project =  $this->project->where(['id' => $projectId, 'deleted_on' => null]);
        return $project ?
            ResponseHelper::sendSuccess($project->first()) : ResponseHelper::notFound();
    }

    public function update(Request $request, $projectId)
    {
        $project =  $this->project->where(['id' => $projectId, 'deleted_on' => null, 'user_id' => Auth::id()])->first();
        if (!$project) {
            return ResponseHelper::notFound();
        }

        $validatedData = $this->validateCreateRequest($request->all());
        if ($validatedData->fails()) {
            return ResponseHelper::badRequest($validatedData->errors()->first());
        }
        $project =  $project->update($request->all());
        return $project ? ResponseHelper::sendSuccess($project) : ResponseHelper::serverError();
        // $project->posted();
        // $project->owner->notify((new ProjectPosted)->delay(10)->onQueue('notifs'));
    }


    public function delete($projectId)
    {
        $project =  $this->project->where(['id' => $projectId, 'deleted_on' => null])->first();
        if (!$project) {
            return ResponseHelper::notFound();
        }
        $project->delete();
        return $project ? ResponseHelper::sendSuccess([]) : ResponseHelper::serverError();
        // $project->owner->notify((new ProjectCancelled)->delay(10));
    }


    public function ajax(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('edit', $project);
        $project->update([$request->field => $request->value]);
        if ($request->field == 'task_id') return SubTask::fetchSubtasksWithTaskId($request->value);
        if ($request->field == 'country_id') return Region::fetchRegionsWithCountryId($request->value);
        if ($request->field == 'region_id') return City::fetchCitiesWithRegionId($request->value);
    }

    // this is where i stop i dont knw what to do with it yet
    private function draftProjects($array)
    {
        return $array->filter(function ($items) {
            return $items->status == 'Draft';
        });
    }

    private function notDraftProjects($array)
    {
        return $array->filter(function ($items) {
            return $items->status != 'Draft';
        });
    }

    private function validateCreateRequest($request)
    {
        return Validator::make($request, [
            'model' => 'required',
            'task_id' => 'required|integer',
            'num_of_taskMaster' => 'required|integer',
            'budget' => 'required',
            'experience' => 'required',
            'proposed_start_date' => 'required',
            'description' => 'required',
            'title' => 'required',
            'sub_task_id' => 'required|integer',
            'country_id' => 'required|integer',
            'city_id' => 'required|integer',
            'duration' => 'required',
        ]);
    }
}
