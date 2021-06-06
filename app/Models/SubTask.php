<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTask extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function subtasks()
    {
        return $this->belongsToMany(User::class, 'user_subtask');
    }


    public function task()
    {
        return $this->belongsTo(Tasks::class);
    }

    public function storeSubtasks($subTaskString, $taskId)
    {
        if (strpos($subTaskString, ",") === false) return $this::create(['task_id' => $taskId, 'name' => $subTaskString]);
        $subTaskArray = explode(",", $subTaskString);
        foreach ($subTaskArray as  $subTask) {
            $this::firstOrcreate(['task_id' => $taskId, 'name' => $subTask]);
        }
    }

    public static function fetchSubtasksWithTaskId ($taskId)
    {
        return SubTask::where('task_id', $taskId)->get(['id', 'name']);
    }
}
