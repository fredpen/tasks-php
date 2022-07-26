<?php

namespace App\Models;

use App\Traits\ProjectTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class Project extends Model
{
    use HasFactory,
        ProjectTraits,
        SoftDeletes;

    protected $guarded = [];

    protected $appends = [
        'project_model',
        "project_status",
        "project_experience"
    ];

    const CANCELED_STATUS = 0;
    const POSTED_STATUS = 1;
    const DELETED_STATUS = 5;

    public function likes()
    {
        return $this->hasMany(FavouredProject::class);
    }

    public function getProjectModelAttribute()
    {
        $model = "0{$this->model}";
        return Config::get("constants.projectModels")[$model];
    }

    public function getProjectExperienceAttribute()
    {
        return Config::get("constants.projectExpertise")[$this->experience];
    }

    public function getProjectStatusAttribute()
    {
        return Config::get("constants.projectStatus")[$this->status] ?? null;
    }

    public function task()
    {
        return $this->belongsTo(Tasks::class, 'task_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function appliable()
    {
        return $this->where('cancelled_on', null)
            ->where('deleted_at', null)
            ->where('assigned_on', null)
            // ->where('hasPaid', 1)
            ->where('posted_on', '!=', null);
    }

    public function scopeLocalWhereIn($query, string $columnName, array $keys)
    {
        return $query->whereIn($columnName, $keys);
    }

    public function subtask()
    {
        return $this->belongsTo(SubTask::class, 'sub_task_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isOwner()
    {
        return $this->user_id == Auth::id() ? true : false;
    }

    public function photos()
    {
        return $this->hasMany(Projectphoto::class);
    }

    public function applications()
    {
        return $this->hasMany(ProjectApplications::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
