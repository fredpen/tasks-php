<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Region;
use App\Helpers\ResponseHelper;
use App\Models\City;
use Illuminate\Support\Facades\Cache;


class LocationController extends Controller
{
    public function countriesOnly()
    {
        if (Cache::has('countriesOnly') && count(Cache::get("countriesOnly"))) {
            return ResponseHelper::sendSuccess(Cache::get('countriesOnly'));
        }

        $countries = Country::query()->orderBy("name", "asc")->get();
        Cache::put('countriesOnly', $countries);

        return ResponseHelper::sendSuccess($countries);
    }

    public function countries()
    {
        if (Cache::has('countries') && count(Cache::get("countries"))) {
            return ResponseHelper::sendSuccess(Cache::get('countries'));
        }

        $countries = Country::with('regions')->orderBy("name", "asc")->get();
        Cache::put('countries', $countries);

        return ResponseHelper::sendSuccess($countries);
    }

    public function regionsOnly()
    {
        if (Cache::has('regionsOnly') && count(Cache::get("regionsOnly"))) {
            return ResponseHelper::sendSuccess(Cache::get('regionsOnly'));
        }

        $regions = Region::query()->orderBy("name", "asc")->get();
        Cache::put('regionsOnly', $regions);

        return ResponseHelper::sendSuccess($regions);
    }

    public function regions($countryId)
    {
        $cacheName = "regions{$countryId}";
        if (Cache::has($cacheName) && count(Cache::get($cacheName))) {
            return ResponseHelper::sendSuccess(Cache::get($cacheName));
        }

        $regions = Region::where('country_id', $countryId)->orderBy("name", "asc")->get(['id', 'name']);
        Cache::put($cacheName, $regions);

        return ResponseHelper::sendSuccess($regions);
    }

    public function cities($regionId)
    {
        $cacheName = "cities{$regionId}";
        if (Cache::has($cacheName) && count(Cache::get($cacheName))) {
            return ResponseHelper::sendSuccess(Cache::get($cacheName));
        }

        $cities =  City::where('region_id', $regionId)->orderBy("name", "asc")->get();
        Cache::put($cacheName, $cities);

        return ResponseHelper::sendSuccess($cities);
    }
}
