<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait UserTraits
{
    public function storeMyFile($requestFile, string $location, int $userId)
    {
        $extension = $requestFile->extension();
        $randomString = "my{$location}file937840jasiu8ygbcj7383737d{$userId}";

        $fileName = "$randomString.{$extension}";
        $url =  $requestFile->storeAs($location, $fileName);

        return "{$url}";
    }

    public function isProfileCompleted()
    {
        $params = Config::get('constants.userSecurityUpdate');
        $canSkip = Config::get('constants.canSkipBeforeApplying');

        foreach ($params as $key) {
            if (!in_array($key, $canSkip) && !$this->$key) {
                return "{$key} is missing";
            }
        }

        if (!$this->skills->count()) {
            return "You have not defined your Skill set";
        }

        return true;
    }

    public function ownsProject($project_id)
    {
        return !!$this->projects->where('id', $project_id)->count();
    }
}
