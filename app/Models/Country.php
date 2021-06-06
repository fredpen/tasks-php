<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    public function regions()
    {
        return $this->hasMany(Region::class);
    }


    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
