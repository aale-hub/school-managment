<?php

declare(strict_types=1);

namespace School\Infrastructure\Api;

class ApiResponse
{
    public static function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public static function success(mixed $data, string $message = 'OK', int $statusCode = 200): void
    {
        self::json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    public static function created(mixed $data, string $message = 'Created'): void
    {
        self::success($data, $message, 201);
    }

    public static function noContent(): void
    {
        http_response_code(204);
    }

    public static function error(string $message, int $statusCode = 400, array $errors = []): void
    {
        $body = [
            'success' => false,
            'message' => $message,
        ];
        if (!empty($errors)) {
            $body['errors'] = $errors;
        }
        self::json($body, $statusCode);
    }

    public static function notFound(string $message = 'Resource not found'): void
    {
        self::error($message, 404);
    }

    public static function serverError(string $message = 'Internal server error'): void
    {
        self::error($message, 500);
    }
}
