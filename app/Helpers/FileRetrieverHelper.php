<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Config;
use App\Helpers\ResponseHelper;

class FileRetrieverHelper
{
    public static function roles()
    {
        $roles = Config::get('constants.roles');
        return count($roles) ? ResponseHelper::sendSuccess($roles) : ResponseHelper::serverError();
    }
    
    public static function duration()
    {
        $roles = Config::get('constants.projectDuration');
        return count($roles) ? ResponseHelper::sendSuccess($roles) : ResponseHelper::serverError();
    }
    
    public static function expertise()
    {
        $roles = Config::get('constants.expertise');
        return count($roles) ? ResponseHelper::sendSuccess($roles) : ResponseHelper::serverError();
    }
    
    public static function fetchCreateOptions()
    {
        $createOptions = [
            'duration' => Config::get('constants.projectDuration'),
            'expertise' => Config::get('constants.expertise'),
            'metaData' => Config::get('constants.metaData'),
        ];
        return $createOptions ? ResponseHelper::sendSuccess($createOptions) : ResponseHelper::serverError();
    }
    
   
}