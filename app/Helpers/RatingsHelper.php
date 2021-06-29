<?php

namespace App\Helpers;

class RatingsHelper
{
    public static function sendSuccess($data, $message = "Success")
    {
        $response = [
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, 200);
    }


}
