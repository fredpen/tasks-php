<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens;

    protected $guarded = [];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function skills()
    {
        return $this->belongsToMany(SubTask::class, 'user_sub_task');
    }

    public function jobs()
    {
        return $this->belongsToMany(Tasks::class, 'user_tasks', 'user_id', 'task_id');
    }

    public function fetchskillsId()
    {
        $skillsObject = $this->skills;
        $skillsArray = json_decode(json_encode($skillsObject), true) ;
        return array_column($skillsArray, 'id');
    }

    public function fetchJobsId()
    {
        $jobsObject = $this->jobs;
        $jobsArray = json_decode(json_encode($jobsObject), true) ;
        return array_column($jobsArray, 'id');
    }

    public function isTaskMaster()
    {
        return $this->role_id == 2 ? true : false;
    }

    public function isTaskGiver()
    {
        return $this->role_id == 1 ? true : false;
    }

    public function isAdmin()
    {
        return $this->role_id === 0 ? true : false;
    }

    public function isActive()
    {
        return $this->isActive === 1 ? true : false;
    }

    public function status()
    {
        return $this->isActive;
    }

    public function hasApplied($project_id)
    {
        $hasApplied = ProjectUser::where(['project_id' => $project_id, 'user_id' => $this->id])->first();
        return $hasApplied ? 1 : 0;
    }

    public function appliedProjects() //applied projects
    {
        return $this->belongsToMany(Project::class, 'project_apllieduser');
    }

    public function assignedProjects() //assigned projects
    {
        return $this->belongsToMany(Project::class, 'project_assigneduser')->withPivot('status');
    }

    public function projects() //created projects
    {
        return $this->hasMany(Project::class);
    }


}
