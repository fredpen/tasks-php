<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany('App\User');
    }
}
// public function roles()
// {
//     return $this->belongsToMany('App\Role', 'role_user_table', 'user_id', 'role_id');
// }
