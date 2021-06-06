<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAppliedUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'project_apllieduser';

    public function myApplications()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function projects()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
