<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectApplications extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $table = 'project_applications';

    public function applicants()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function projects()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }


    public static function userAppliedToProject($projectId, $user_id)
    {
        return !!Self::query()
            ->where("project_id", $projectId)
            ->where("user_id", $user_id)
            ->count();
    }
}
