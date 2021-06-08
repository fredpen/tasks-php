<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens, HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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

    public function subTasks()
    {
        return $this->belongsToMany(SubTask::class, 'user_sub_task');
    }

    public function tasks()
    {
        return $this->belongsToMany(Tasks::class, 'user_tasks', 'user_id', 'task_id');
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

    public function myApplications()
    {
        return $this->hasMany(ProjectApplications::class);
    }

    public function projects() //created projects
    {
        return $this->hasMany(Project::class);
    }
}
