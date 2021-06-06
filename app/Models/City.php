<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $hidden = ['latitude', 'longitude', 'created_at', 'updated_at'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public static function fetchCitiesWithRegionId($regionId)
    {
        return City::where('region_id', $regionId)->get(['id', 'name']);
    }
}
