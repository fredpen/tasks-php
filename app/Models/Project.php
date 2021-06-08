<?php

namespace App\Models;

use App\Traits\ProjectTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Project extends Model
{
    use HasFactory, ProjectTraits, SoftDeletes;

    protected $guarded = [];

    public function task()
    {
        return $this->belongsTo(Tasks::class, 'task_id');
    }

    public function subtask()
    {
        return $this->belongsTo(SubTask::class, 'sub_task_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function photos()
    {
        return $this->hasMany(Projectphoto::class);
    }

    public function aplliedUser()
    {
        return $this->belongsToMany(User::class, 'project_apllieduser')->withPivot('resume');
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

    public function completed()
    {
        return $this->update(['status' => 'completed', 'completed_on' => $this->timeNow()]);
    }

    public function cancelled()
    {
        if ($this->status == 'Draft')  return $this->delete();
        return $this->update(['status' => 'cancelled', 'cancelled_on' => $this->timeNow()]);
    }

    public function live()
    {
        return $this->update(['status' => 'started', 'started_on' => $this->timeNow()]);
    }

    public function markCreate()
    {
        return $this->update(['status' => 'Draft']);
    }

    public function posted()
    {
        return $this->update(['status' => 'posted', 'posted_on' => $this->timeNow()]);
    }
}
