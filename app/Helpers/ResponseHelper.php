<?php

namespace App\Helpers;


class ResponseHelper
{
    public static function sendSuccess($data, $message = "Success")
    {
        $response = [
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, 200);
    }

    public static function notFound()
    {
        $response = [
            'message' => 'Resources not available',
        ];
        return response()->json($response, 404);
    }

    public static function unAuthorised($message = "Unauthorised")
    {
        $response = [
            'message' => $message,
        ];
        return response()->json($response, 401);
    }

    public static function badRequest($message)
    {
        $response = [
            'message' => $message
        ];
        return response()->json($response, 400);
    }

    public static function serverError()
    {
        $response = [
            'message' => 'Internal server error'
        ];
        return response()->json($response, 500);
    }
}
