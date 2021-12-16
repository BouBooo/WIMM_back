<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse {

    static $success = 'success';
    static $error = 'error';

    protected function respond(string $message, $data = [], int $httpCode = Response::HTTP_OK, string $status = null): JsonResponse
    {
        return new JsonResponse([
            'status' => $status ?? self::$success,
            'message' => $message,
            'data' => $data,
        ], $httpCode);
    }

    protected function respondWithError(string $message, $data = [], int $httpCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->respond($message, $data, $httpCode, self::$error);
    }
}
