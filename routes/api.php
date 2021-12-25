<?php

use App\Http\Controllers\Plaid\AccountController;
use App\Http\Controllers\Plaid\TokenAccessController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], static function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

Route::group([
    'middleware' => 'jwt.verify',
], static function ($router) {
    Route::get('/user-profile', [UserController::class, 'userProfile']);
    Route::post('/user-profile', [UserController::class, 'update']);
    Route::post('/plaid/link-token/create', [TokenAccessController::class, 'createLinkToken']);
    Route::post('/plaid/public-token/exchange', [TokenAccessController::class, 'exchangePublicToken']);
    Route::get('/plaid/auth/get', [AccountController::class, 'authData']);
});

Route::any('{any}', static function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Resource not found',
        'data' => [],
    ], Response::HTTP_NOT_FOUND);
})->where('any', '.*');
