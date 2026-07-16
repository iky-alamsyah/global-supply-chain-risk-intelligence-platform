<?php

declare(strict_types=1);

namespace App\Helpers;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200
    ): array {
        return [
            'success' => true,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
    }

    public static function error(
        string $message = 'Error',
        int $status = 500,
        mixed $errors = null
    ): array {
        return [
            'success' => false,
            'status' => $status,
            'message' => $message,
            'errors' => $errors,
        ];
    }
}