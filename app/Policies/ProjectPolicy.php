<?php

namespace App\Policies;

class ProjectPolicy
{
    public static function edit($user, $project)
    {
        if ($user->isAdmin() || $project->user_id == $user->id) {
            return true;
        }
        return false;
    }
}
