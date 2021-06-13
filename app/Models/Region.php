<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public static function fetchRegionsWithCountryId ($countryId)
    {
        return Region::where('country_id', $countryId)->get(['id', 'name']);
    }
}
