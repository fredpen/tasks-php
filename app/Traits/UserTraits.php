<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait UserTraits
{
    public function isProfileCompleted()
    {
        $params = Config::get('constants.userSecurityUpdate');
        unset($params['linkedln']);

        foreach ($params as $key) {
            if (!$this->$key) {
                return "{$key} is missing";
            }
        }

        if (!$this->skills->count()) {
            return "Skills is missing";
        }

        return true;
    }
}
