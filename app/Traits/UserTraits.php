<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait UserTraits
{
    public function storeMyFile($requestFile, string $location, int $userId)
    {
        $baseUrl = Config::get('app.url');
        $extension = $requestFile->extension();
        $randomString = "my{$location}file937840jasiu8ygbcj7383737d{$userId}";

        $fileName = "$randomString.{$extension}";
        $url =  $requestFile->storeAs($location, $fileName);

        return "{$baseUrl}/storage/{$url}";
    }

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