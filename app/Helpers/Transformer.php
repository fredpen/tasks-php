<?php

namespace App\Helpers;

class Transformer
{
    public static function workHistoryEmployer($data)
    {
        return $data->map(function ($item, $key) {
            return [
                "project_id" => $item->id,
                "project_title" => $item->title,
                "created_at" => $item->created_at,
                "project_desc" => $item->description,
                "application_id" => count($item->applications) > 0 ? $item->applications[0]->project_id : null,
                "rating" => count($item->applications) > 0 ? $item->applications[0]->taskMaster_rating : null,
                "comment" => count($item->applications) > 0 ? $item->applications[0]->taskMaster_comment : null,
            ];
        });
    }

    public static function workHistoryFreelancer($data)
    {
        return $data->map(function ($item, $key) {
            return [
                "project_id" => $item->project_id,
               
                "project_desc" => $item->projects ? $item->projects->description : null,
                "created_at" => $item->projects ? $item->projects->created_at : null,
                "project_title" => $item->projects ? $item->projects->title : null,
                "application_id" => $item->id,
                "rating" => $item->owner_rating,
                "comment" => $item->owner_comment
            ];
        });
    }

    public static function userDetails($data)
    {
        return $data->map(function ($item, $key) {
            return [
                "title" => $item->title,
                "name" => $item->name,
                "address" => $item->address,
                "email" => $item->email,
                "avatar" => $item->avatar,
                "ratings" => $item->ratings,
                "bio" => $item->bio,
                "country" => $item->country ? $item->country->name : null,
                "region" => $item->region ? $item->region->name : null,
                "city" => $item->city ? $item->city->name : null,
                "linkedln" => $item->linkedln,
            ];
        })->first();
    }

    public static function skills($data)
    {
        return $data->skills->map(function ($item, $key) {
            return [
                "id" => $item->skill->id,
                "title" => $item->skill->name,
            ];
        });
    }
}
