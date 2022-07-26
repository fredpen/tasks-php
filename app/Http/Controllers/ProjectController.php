<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use App\Helpers\ResponseHelper;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;

use App\Http\Requests\{
    ProjectCreateRequest,
    ProjectUpdateRequest
};
use App\Models\{
    FavouredProject,
    Hjobs,
    Project,
    User
};

class ProjectController extends Controller
{
    public function relatedProjects(Request $request)
    {
        $project = Project::find($request->project_id);
        if (!$project) {
            return ResponseHelper::notFound('Invalid project Id');
        }

        $projects = Project::where('id', '!=', $project->id)
            ->where(function ($query) use ($project) {
                $query->where('task_id', $project->task_id)
                    ->orWhere('region_id', $project->region_id)
                    ->orWhere('model', $project->model);
            });

        $projects = $projects
            ->with(Config::get('protectedWith.project'))
            ->inRandomOrder()
            ->take(4)
            ->get();

        return ResponseHelper::sendSuccess($projects, 'successful');
    }

    public function projectAttributes()
    {
        // what is needed to create a project
        $attributes = (new Project())->attributes();
        return ResponseHelper::sendSuccess($attributes, 'successful');
    }

    public function searchProject(Request $request)
    {
        return ResponseHelper::sendSuccess([], "use all projects with filter");
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
            return ResponseHelper::sendSuccess([], "Query returns empty");
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
        $request->validate(["project_id" => "required"]);

        try {
            $request->user()
                ->likedProjects()
                ->where('project_id', $request->project_id)
                ->delete();
            //
        } catch (\Throwable $th) {
            return ResponseHelper::serverError($th->getMessage());
        }

        return ResponseHelper::sendSuccess();
    }

    public function favouredAProject(Request $request)
    {
        $request->validate(["project_id" => "required"]);
        try {
            Project::fetchProject($request->project_id)
                ->firstOrCreate(
                    ["user_id" => $request->user()->id],
                    ["project_id" => $request->project_id]
                );
        } catch (\Throwable $th) {
            return ResponseHelper::badRequest($th->getMessage());
        }

        return  ResponseHelper::sendSuccess();
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


    public function store(ProjectCreateRequest $request)
    {
        $data = $request->validated();
        $data['num_of_taskMaster'] = 1;
        $data['user_id'] = $request->user()->id;

        try {
            $project = Project::create($data);

            $request->user()->notify((new GeneralNotification(
                "Project Creation",
                "Your project has been created",
                false,
                "#HJOOBS",
                false
            )));
            //
        } catch (\Throwable $th) {
            return  ResponseHelper::badRequest($th->getMessage());
        }

        return ResponseHelper::sendSuccess($project);
    }

    public function publish($projectId)
    {
        try {
            $project = Project::fetchProject($projectId);

            $project = $project->isPublishable()
                ->update(['posted_on' => now(), "status" => Project::POSTED_STATUS]);

            $project->owner()->notify((new GeneralNotification(
                "Project Published",
                "Your project has been published",
                false,
                Hjobs::APP_NAME,
                false
            )));
            //
        } catch (\Throwable $th) {
            return ResponseHelper::badRequest($th->getMessage());
        }

        return ResponseHelper::sendSuccess([], 'Project is now live');
    }

    public function update(ProjectUpdateRequest $request)
    {
        try {
            Project::fetchProject($request->project_id)
                ->update($request->except(['id', 'user_id', 'isActive', 'status', 'posted_on', 'started_on', 'completed_on', 'cancelled_on', 'deleted_on', 'project_id']));
            //
        } catch (\Throwable $th) {
            return ResponseHelper::badRequest($th->getMessage());
        }

        return ResponseHelper::sendSuccess();
    }

    public function cancel(Request $request)
    {
        $request->validate(['project_id' => 'required']);

        try {
            $project =  Project::fetchProject($request->project_id)
                ->isCancellable()
                ->update([
                    'cancelled_on' => now(),
                    "status" => Project::CANCELED_STATUS
                ]);

            $project->owner()->notify((new GeneralNotification(
                "Project cancellation",
                "Your project has been cancelled",
                false,
                Hjobs::APP_NAME,
                false
            )));
            //
        } catch (\Throwable $th) {
            return ResponseHelper::badRequest($th->getMessage());
        }

        return ResponseHelper::sendSuccess();
    }

    public function delete(Request $request)
    {
        $request->validate(['project_id' => 'required']);

        try {
            $project =  Project::fetchProject($request->project_id)
                ->isDeletable()
                ->update([
                    'deleted_at' => now(),
                    "status" => Project::DELETED_STATUS
                ]);

            $project->owner()->notify((new GeneralNotification(
                "Project deletion",
                "Your project has been deleted",
                false,
                Hjobs::APP_NAME,
                false
            )));
            //
        } catch (\Throwable $th) {
            return ResponseHelper::badRequest($th->getMessage());
        }

        return ResponseHelper::sendSuccess();

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


    private function validateSearchRequest(Request $request)
    {
        return $request->validate([
            'description' => 'sometimes|string',
            'task_id' => 'sometimes|array|min:1',
            'sub_task_id' => 'sometimes|array|min:1',
            'country_id' => 'sometimes|array|min:1',
            'region_id' => 'sometimes|array|min:1',
            'city_id' => 'sometimes|array|min:1',
            'model' => 'sometimes|array|min:1|max:2',
            'experience' =>  'sometimes|array|min:1',
        ]);
    }

    ///usrers
    public function search(Request $request)
    {
        $searchTerm = $request->searchTerm;
        $lookUp = ["my_drafts", "my_cancelled_projects", "my_completed_projects", "my_running_projects", "my_favourites", "my_projects", "my_published_projects"];

        if (!in_array($searchTerm, $lookUp)) {
            return ResponseHelper::invalidRoute("Invalid identifier '{$searchTerm}'");
        }

        $projects = $this->{$searchTerm}($request->user());

        return $this->paginateMe(
            $projects->with(Config::get('protectedWith.project'))
        );
    }

    private function my_cancelled_projects(User $user)
    {
        return $user->projects()->where('cancelled_on', '!=', null);
    }


    private function my_published_projects(User $user)
    {
        return $user->projects()
            ->where('posted_on', '!=', null)
            ->where('cancelled_on', null)
            ->where('completed_on', null);
    }

    private function my_running_projects(User $user)
    {
        return $user->projects()
            ->where('started_on', '!=', null)
            ->where('posted_on', '!=', null)
            ->where('cancelled_on', null)
            ->where('completed_on', null);
    }

    private function my_drafts(User $user)
    {
        return $user->projects()
            ->where('posted_on', null)
            ->where("cancelled_on", null)
            ->where('completed_on', null)
            ->where("deleted_at", null);
    }


    private function my_projects(User $user)
    {
        return $user->projects();
    }

    private function my_completed_projects(User $user)
    {
        return $user->projects()
            ->where('started_on', '!=', null)
            ->where('posted_on', '!=', null)
            ->where('cancelled_on', null)
            ->where('completed_on', '!=', null)
            ->with(['applications' => function ($query) {
                $query->where('assigned', '!=', null)
                    ->where('hasAccepted', '!=', null);
            }]);
    }


    private function my_favourites(User $user)
    {
        $ids = $user->likedProjects()->distinct()->pluck('project_id');
        return Project::whereIn('id', $ids);
    }

    public function favouriteProjectsIds(Request $request)
    {
        return $request->user()
            ->likedProjects()
            ->distinct()
            ->pluck('project_id');
    }
}
