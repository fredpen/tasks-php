<?php

namespace App\Models;

use App\Traits\ProjectTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Project extends Model
{
    use HasFactory, ProjectTraits, SoftDeletes;

    protected $guarded = [];

    public function likes()
    {
        return $this->hasMany(FavouredProject::class);
    }

    public function task()
    {
        return $this->belongsTo(Tasks::class, 'task_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
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
