<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected $with = ['task:id,name', 'subtask:id,name', 'owner:id,name', 'country:id,name', 'region:id,name', 'city:id,name', 'photos:url,project_id'];

    public function timeNow()
    {
        return Carbon::now();
    }

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

    public function taskMasters()
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

    public function isAssigned()
    {
        return count(DB::table('project_assigneduser')->where('project_id', $this->id)->get('id')) > 0;
    }

    public function hasBeenAssigned($user_id)
    {
        $project = DB::table('project_assigneduser')->where([
            'project_id' => $this->id,
            'user_id' => $user_id
        ])->get();
        return count($project) > 0;
    }

    public function updateStatus($status)
    {
        if ($status == "completed") {
            return $this->completed();
        } elseif ($status == "cancelled") {
            return $this->cancelled();
        } elseif ($status == "deleted") {
            return $this->delete();
        } elseif ($status == "started") {
            return $this->live();
        } elseif ($status == "posted") {
            return $this->posted();
        }
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

    public function delete()
    {
        return $this->update(['status' => 'deleted', 'deleted_on' => $this->timeNow()]);
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

    public function color()
    {
        if ($this->status == 'Draft') return "primary";
        if ($this->status == 'posted') return "secondary";
        if ($this->status == 'completed') return "success";
        if ($this->status == 'live') return "secondary";
    }

}


