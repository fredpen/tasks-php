<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAssignedUser extends Model
{
    use HasFactory;
    
    protected $table = "project_assigneduser";

    protected $with = ['assignedUser'];

    protected $guarded = [];


    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedProject()
    {
        return $this->belongsToMany(Project::class, 'project_id');
    }

}
