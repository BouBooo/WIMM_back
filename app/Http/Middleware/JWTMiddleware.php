<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Exception;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;
use Symfony\Component\HttpFoundation\Response;

class JWTMiddleware extends BaseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json(['status' => 'Token is Invalid'], Response::HTTP_FORBIDDEN);
            }

            if ($e instanceof TokenExpiredException) {
                return response()->json(['status' => 'Token is Expired'], Response::HTTP_UNAUTHORIZED);
            }

            return response()->json(['status' => 'Authorization Token not found'], Response::HTTP_NOT_FOUND);
        }

        return $next($request);
    }
}
