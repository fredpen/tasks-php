<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Project;
use App\Models\Projectphoto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class ProjectphotoController extends Controller
{
    public function addMedia(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'attachments' => ['required', 'image', 'max:2000000']
        ]);

        $project =  Project::query()->where('id', $request->project_id);
        if (!$project->count()) {
            return ResponseHelper::notFound("Invalid Project ID");
        }

        $project = $project->first();
        $files = $request->file("attachments");

        if (!is_array($files)) {
            $uploadedMediaUrl = $this->uploadMedia($files, $project->id);
            $createMedia =  Projectphoto::create(['url' => $uploadedMediaUrl, "project_id" => $project->id]);
        } else {
            $requestObject = [];
            foreach ($files as $file) {
                $uploadedMediaUrl = $this->uploadMedia($files, $project->id);
                $requestObject[] = ['url' => $uploadedMediaUrl, "project_id" => $project->id];
            }

            $createMedia =  Projectphoto::createMany($requestObject);
        }

        return $createMedia ?
            ResponseHelper::sendSuccess([], 'Media attached to project successfully') : ResponseHelper::serverError();
    }

    public function removeMedia(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'attachment_ids' => ['required','array']
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
            ResponseHelper::sendSuccess([], 'Media removed to project successfully') : ResponseHelper::serverError();

    }

    private function deleteMediaFile(Collection $projectphotos)
    {
        $baseUrl = Config::get('app.url') . "/storage/";

        $locationArray = [];
        foreach ($projectphotos as $photo) {
            $locationArray[] = str_replace($baseUrl, "", $photo->url);
        }

        Storage::delete($locationArray);
    }

    private function uploadMedia($requestFiles, int $projectId)
    {
        $baseUrl = Config::get('app.url');
        $url =  $requestFiles->store("projects/gracious{$projectId}");

        return "{$baseUrl}/storage/{$url}";
    }



    // public function store(Request $request, Projectphoto $projectphoto)
    // {
    //     $file = $request->file('file');
    //     $image_name = $projectphoto->saveFile($file);

    //     if ($userId = $request->profilePicture) {
    //         $user = User::findOrFail($userId);
    //         $user->update(['imageurl' => $image_name]);
    //     } else {
    //         $project = Project::findOrFail($request->project_id);
    //         if ($project->photos->count() >= 3) return false;
    //         $projectphoto->create(['url' => $image_name, 'project_id' => $request->project_id]);
    //     }
    // }


    // public function destroy($id)
    // {
    //     $file = Projectphoto::findOrFail($id);
    //     unlink(public_path() . "/images/".$file->url);
    //     $file->delete();
    // }
}
