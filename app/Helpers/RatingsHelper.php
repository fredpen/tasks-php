<?php

namespace App\Helpers;

use App\Models\ProjectApplications;
class RatingsHelper
{
    public static function rate(ProjectApplications $application, bool $isOwner, int $ratings, string $comments)
    {
        $ratingColumn = $isOwner ? "owner_rating" : "taskMaster_rating";
        $commentColumn = $isOwner ? "owner_comment" : "taskMaster_comment";

        return $application->update([
            $ratingColumn => $ratings,
            $commentColumn => $comments
        ]);
    }

    public static function canRate(ProjectApplications $application)
    {
    }
}
