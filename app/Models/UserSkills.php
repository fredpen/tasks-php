<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSkills extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = "user_skills";

    protected $hidden = ['created_at', 'updated_at'];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function skill()
    {
        return $this->belongsTo(SubTask::class, 'sub_task_id');
    }

    public function addSkills()
    {
        return $this->belongsTo(SubTask::class, 'sub_task_id');
    }


}
