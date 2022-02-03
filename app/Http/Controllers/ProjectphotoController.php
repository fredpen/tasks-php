<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\Projectphoto;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class ProjectphotoController extends Controller
{
    public function addMedia(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'attachment' => ['required', 'file', 'max:2000'],
        ]);

        try {
            $project =  Project::findorFail($request->project_id);
            $files = $request->file("attachment");
            $url = $this->uploadMedia($files, $project->id);
            $project->photos()->create(['url' => $url]);
        } catch (\Throwable $th) {
            return ResponseHelper::serverError($th->getMessage());
        }

        return  ResponseHelper::sendSuccess([], 'Media attached to project successfully');
    }

    public function removeMedia(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'attachment_ids' => ['required', 'array', "min:1"]
        ]);

        $projectphotos = Projectphoto::query()
            ->whereIn('id', $request->attachment_ids)
            ->where('project_id', $request->project_id);

        if (!$projectphotos->count()) {
            return ResponseHelper::notFound("one or more attachment does not belongs to the project");
        }

        $this->deleteMediaFile($projectphotos->get());
        $deleteMedia = $projectphotos->delete();

        return $deleteMedia ?
            ResponseHelper::sendSuccess([], 'Media removed from project successfully') :
            ResponseHelper::serverError();
    }

    private function deleteMediaFile(Collection $projectphotos)
    {
        $baseUrl = Config::get('app.url') . "/storage/";
        $urls = $projectphotos->each(fn ($photo) => str_replace($baseUrl, "", $photo->url));

        return Storage::delete($urls);
    }

    private function uploadMedia($requestFiles, int $projectId)
    {
        $baseUrl = Config::get('app.url');
        $url =  $requestFiles->store("projects/gracious{$projectId}");

        return "{$baseUrl}/storage/{$url}";
    }
}
