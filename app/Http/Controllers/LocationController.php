<?php

namespace App\Http\Controllers;

use App\Country;
use App\Region;
use App\City;
use App\Helpers\ResponseHelper;

class LocationController extends Controller
{
    protected $city;
    protected $country;
    protected $region;

    public function __construct(Country $country, Region $region, City $city)
    {
        $this->city = $city;
        $this->region = $region;
        $this->country = $country;
    }

    public function countries()
    {
        $countries = $this->country->all();
        return $countries ? ResponseHelper::sendSuccess($countries) : ResponseHelper::notFound();
    }

    public function regions($countryId)
    {
        $regions = $this->region->where('country_id', $countryId)->get();
        return $regions ? ResponseHelper::sendSuccess($regions) : ResponseHelper::notFound();
    }
   
    public function cities($regionId)
    {
        $cities = $this->city->where('region_id', $regionId)->get();
        return $cities ? ResponseHelper::sendSuccess($cities) : ResponseHelper::notFound();
    }

}
