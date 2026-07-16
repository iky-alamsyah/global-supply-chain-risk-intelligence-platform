<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiTrait
{
    protected function successResponse(
        mixed $data,
        string $message = 'Success',
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function errorResponse(
        string $message = 'Error',
        int $status = 500
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}