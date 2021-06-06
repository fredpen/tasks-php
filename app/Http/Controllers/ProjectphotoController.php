<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Projectphoto;
use App\Project;
use App\User;

class ProjectphotoController extends Controller
{
    public function store(Request $request, Projectphoto $projectphoto)
    {
        $file = $request->file('file');
        $image_name = $projectphoto->saveFile($file);
        
        if ($userId = $request->profilePicture) {
            $user = User::findOrFail($userId);
            $user->update(['imageurl' => $image_name]);
        } else {
            $project = Project::findOrFail($request->project_id);
            if ($project->photos->count() >= 3) return false;
            $projectphoto->create(['url' => $image_name, 'project_id' => $request->project_id]);
        }
    }

   
    public function destroy($id)
    {
        $file = Projectphoto::findOrFail($id);
        unlink(public_path() . "/images/".$file->url);
        $file->delete();
    }
}
