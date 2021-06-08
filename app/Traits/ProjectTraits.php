<?php

namespace App\Traits;

use App\Models\ProjectApplications;
use Illuminate\Support\Facades\Config;

trait ProjectTraits
{
    public function isPublishable()
    {
        $params = Config::get('constants.canPublish');

        foreach ($params as $key => $value) {
            if (!$this->$key) {
                return "The project requires {$value} to be publishable";
            }
        }

        return true;
    }

    public function isDeletable()
    {
        return $this->isAssigned() ?
            "You can't delete because the project has been assigned" : true;
    }

    public function openForApplications()
    {
        return $this->isAssigned() ? false : true;
    }

    public function isAssigned()
    {
        return !!ProjectApplications::query()
            ->where('project_id', $this->id)
            ->where('assigned', true)
            ->count();
    }
}
