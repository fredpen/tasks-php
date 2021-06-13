<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    public function subTasks()
    {
        return $this->hasMany(SubTask::class, 'task_id');
    }

    public function masters()
    {
        return $this->belongsToMany(User::class, 'user_tasks', 'task_id', 'user_id');
    }

    public function storeTasks($taskString)
    {
        if (strpos($taskString, ",") === false) return $this::create(['name' => $taskString]);
        $taskArray = explode(",", $taskString);
        foreach ($taskArray as  $taskName) {
            $this::firstOrcreate(['name' => $taskName]);
        }
        return true;
    }
}
