<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public const SUCCESS = 'success';
    public const ERROR = 'error';

    protected function respond(string $message, $data = [], int $httpCode = Response::HTTP_OK, string $status = self::SUCCESS): JsonResponse
    {
        return new JsonResponse([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $httpCode);
    }

    protected function respondWithError(string $message, $data = [], int $httpCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->respond($message, $data, $httpCode, self::ERROR);
    }
}
