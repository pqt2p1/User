<?php

namespace Pqt2p1\User\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function errorResponse($message = '', $result = []): JsonResponse
    {
        return response()->json([
            'error'         => 1,
            'mes'           => empty($message) ? '' : $message,
            'result'        => empty($result) ? [] : $result,
        ]);
    }

    public static function successResponse($message = '', $result = []): JsonResponse
    {
        return response()->json([
            'error'         => 1,
            'mes'           => empty($message) ? '' : $message,
            'result'        => empty($result) ? [] : $result,
        ]);
    }
}

