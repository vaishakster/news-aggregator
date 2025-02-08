<?php

namespace App\Helpers;

class ResponseHelper
{
    /**
     * Generate a success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = [], $message = 'Request was successful', $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Generate an error response.
     *
     * @param string $message
     * @param int $statusCode
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($message = 'An error occurred', $statusCode = 500, $data = [])
    {
        return response()->json([
            'status' => 'failed',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
}
