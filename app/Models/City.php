<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{

    public function region ()
    {
        return $this->belongsTo(Region::class);
    }

    public static function fetchCitiesWithRegionId ($regionId)
    {
        return City::where('region_id', $regionId)->get(['id', 'name']);
    }


}
