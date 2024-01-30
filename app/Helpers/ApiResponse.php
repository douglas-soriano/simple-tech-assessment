<?php

namespace App\Helpers;

class ApiResponse
{

    # Default success response for our API endpoints.
    public static function success($data)
    {
        return response()->json(['success' => true, 'response' => $data]);
    }

    # Default error response for our API endpoints.
    public static function error($message, $statusCode = 500)
    {
        return response()->json(['success' => false, 'error' => $message, 'status_code' => $statusCode]);
    }
}