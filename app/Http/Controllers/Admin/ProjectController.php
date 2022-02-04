<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ProjectController extends Controller
{

    public function search(string $searchTerm)
    {
        $lookUp = collect(["drafts", "published", "started", "completed", "cancelled", "deleted"]);

        if (!$lookUp->contains($searchTerm)) {
            return ResponseHelper::notFound("Invalid identifier '{$searchTerm}'");
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

    private function drafts()
    {
        return Project::where('posted_on', null)
            ->where("cancelled_on", null)
            ->where("deleted_at", null);
    }

    private function published()
    {
        return Project::where('posted_on', '!=', null)
            ->where("cancelled_on", null)
            ->where("deleted_at", null);
    }

    private function started()
    {
        return Project::where('started_on', '!=', null)
            ->where("cancelled_on", null)
            ->where("deleted_at", null);
    }

    private function completed()
    {
        return Project::where('completed_on', '!=', null)
            ->where("cancelled_on", null)
            ->where("deleted_at", null);
    }

    private function cancelled()
    {
        return Project::where('cancelled_on', '!=', null);
    }

    private function deleted()
    {
        return Project::where('deleted_at', '!=', null);
    }
}
