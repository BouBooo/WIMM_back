<?php

namespace App\Http\Middleware;

use App\Http\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Exception;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JWTMiddleware extends BaseMiddleware
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): JsonResponse|Closure
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return $this->respondWithError('Token is Invalid', [], Response::HTTP_FORBIDDEN);
            }

            if ($e instanceof TokenExpiredException) {
                return $this->respondWithError('Token is Expired', [], Response::HTTP_FORBIDDEN);
            }

            return $this->respondWithError('Authorization Token not found', [], Response::HTTP_NOT_FOUND);
        }

        return $next($request);
    }
}
