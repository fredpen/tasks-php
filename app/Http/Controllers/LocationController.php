<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Region;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Cache;


class LocationController extends Controller
{
    public function countriesOnly()
    {
        if (Cache::has('countriesOnly')) {
            return ResponseHelper::sendSuccess(Cache::get('countriesOnly'));
        }

        $countries = Country::query();
        if (!$countries->count()) {
            return ResponseHelper::notFound();
        }

        $countries = $countries->get();
        Cache::put('countriesOnly', $countries);

        return ResponseHelper::sendSuccess($countries);
    }

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

    public function regionsOnly()
    {
        if (Cache::has('regionsOnly')) {
            return ResponseHelper::sendSuccess(Cache::get('regionsOnly'));
        }

        $regions = Region::query();
        if (!$regions->count()) {
            return ResponseHelper::notFound();
        }

        $regions = $regions->get();
        Cache::put('regionsOnly', $regions);

        return ResponseHelper::sendSuccess($regions);
    }

    public function regions($countryId)
    {
        $country = Country::query()->where('id', $countryId);
        if (!$country->count()) {
            return ResponseHelper::notFound("Invalid country Id");
        }

        $country = $country->first();
        if (!$country->regions->count()) {
            return ResponseHelper::notFound("There is no region under this country");
        }

        $cacheName = "regions{$countryId}";
        if (Cache::has($cacheName)) {
            return ResponseHelper::sendSuccess(Cache::get($cacheName));
        }

        $regions = $country->regions;
        Cache::put($cacheName, $regions);

        return ResponseHelper::sendSuccess($regions);
    }

    public function cities($regionId)
    {
        $region = Region::query()->where('id', $regionId);
        if (!$region->count()) {
            return ResponseHelper::notFound("Invalid region Id");
        }

        $region = $region->first();
        if (!$region->cities->count()) {
            return ResponseHelper::notFound("There is no city under this region");
        }

        $cacheName = "cities{$regionId}";
        if (Cache::has($cacheName)) {
            return ResponseHelper::sendSuccess(Cache::get($cacheName));
        }

        $cities = $region->cities;
        Cache::put($cacheName, $cities);

        return ResponseHelper::sendSuccess($cities);
    }
}
