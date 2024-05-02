<?php

namespace App\Traits;

trait ApiResponse
{
    public function responseJson($statusCode, $success, $message, $data = [])
    {
        return response()->json([
            'status_code' => $statusCode,
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}
