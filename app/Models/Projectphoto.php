<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projectphoto extends Model
{
    protected $guarded = [];


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function saveFile($file)
    {
        $image_name =  time() . $file->getClientOriginalName();
        $file->move('images/', $image_name);
        return $image_name;
    }
}


