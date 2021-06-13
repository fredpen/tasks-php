<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTask extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    public function subtasks()
    {
        return $this->belongsToMany(User::class, 'user_subtask');
    }


    public function task()
    {
        return $this->belongsTo(Tasks::class);
    }
}
