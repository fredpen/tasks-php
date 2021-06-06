<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Cache;


class LocationController extends Controller
{
    public function countries()
    {
        if (Cache::has('countries')) {
            return ResponseHelper::sendSuccess(Cache::get('countries'));
        }

        $countries = Country::query();
        if (!$countries->count()) {
            return ResponseHelper::notFound();
        }

        $countries = $countries->with('regions')->get();
        Cache::put('countries', $countries);

        return ResponseHelper::sendSuccess($countries);
    }

    public function regions($countryId)
    {
        if (Cache::has('regions')) {
            return ResponseHelper::sendSuccess(Cache::get('regions'));
        }

        $regions = Region::query()->where('country_id', $countryId);
        if (!$regions->count()) {
            return ResponseHelper::notFound();
        }

        $regions = $regions->with('cities')->get();
        Cache::put('regions', $regions);

        return ResponseHelper::sendSuccess($regions);
    }

    public function cities($regionId)
    {
        if (Cache::has('cities')) {
            return ResponseHelper::sendSuccess(Cache::get('cities'));
        }

        $cities = City::query();
        if (!$cities->count()) {
            return ResponseHelper::notFound();
        }

        $cities = $cities->get();
        Cache::put('cities', $cities);

        return ResponseHelper::sendSuccess($cities);
    }
}
