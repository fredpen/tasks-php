<?php

namespace App\Models;

use App\Traits\UserTraits;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable,
        UserTraits,
        HasApiTokens,
        SoftDeletes,
        HasFactory;

    protected $guarded = [];

    protected $appends = [
        'account_secured',
        // 'can_apply',
        'is_admin',
        "user_role"
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
        'security_answer',
        'canApply',
        'remember_token',
        'identification',
        'access_code'
    ];

    public function getUserRoleAttribute()
    {
        $role = "0{$this->role_id}";
        return $role && Config::get("constants.roles") ?
            Config::get("constants.roles")[$role] : null;
    }

    public function getCanApplyAttribute()
    {
        return !!$this->security_answer &&
            $this->identification &&
            $this->has('skills');
    }

    public function getIsAdminAttribute()
    {
        return $this->isAdmin();
    }

    public function getAvatarAttribute($value)
    {
        $baseUrl = Config::get('app.url');
        return $value ?
        Storage::url($value) :
        // "{$baseUrl}/{$value}" :
         "kdkdkd";
    }

    public function getAccountSecuredAttribute()
    {
        return !!$this->security_answer;
    }


    public function likedProjects()
    {
        return $this->hasMany(FavouredProject::class);
    }

    public function skills()
    {
        return $this->hasMany(UserSkills::class);
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

    public function payments() //money you paid
    {
        return $this->hasMany(Payment::class);
    }
}
