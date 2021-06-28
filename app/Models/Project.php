<?php

namespace App\Models;

use App\Traits\ProjectTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

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
