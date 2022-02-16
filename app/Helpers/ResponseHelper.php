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

    public static function invalidRoute($message = "not found")
    {
        return response()->json(['message' => $message], 404);
    }

    public static function successNoContent()
    {
        return response()->json(['message' => "Success"], 204);
    }

    public static function notFound($message = "Resources not available")
    {
        $response = [
            'message' => $message,
        ];
        return response()->json($response, 204);
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

    public static function serverError($message = "Internal server error")
    {
        $response = [
            'message' => $message
        ];
        return response()->json($response, 500);
    }
}
