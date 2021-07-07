<?php

namespace App\Helpers;

use App\Models\Project;
use App\Models\ProjectApplications;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RatingsHelper
{
    public static function rate(ProjectApplications $application, Project $project, bool $isOwner, int $ratings, string $comments)
    {
        return DB::transaction(function () use ($isOwner, $application, $ratings, $comments, $project) {
            $ratingColumn = $isOwner ? "owner_rating" : "taskMaster_rating";
            $commentColumn = $isOwner ? "owner_comment" : "taskMaster_comment";

            if ($application->$ratingColumn) {
                return "You have rated this service before";
            }

            $application->update([
                $ratingColumn => $ratings,
                $commentColumn => $comments
            ]);

            $userId = $isOwner ? $application->user_id : $project->user_id;
            $ratedUser = User::find($userId);

            $ratedUser->update([
                "ratings" => $ratedUser->ratings + $ratings,
                "ratings_count" => $ratedUser->ratings_count + 1
            ]);

            return true;
        }, 2);
    }
}
