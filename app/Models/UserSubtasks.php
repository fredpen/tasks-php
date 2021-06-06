<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubtasks extends Model
{
    use HasFactory;
    
    protected $table = 'user_sub_task';

    protected $guarded = [];
}
