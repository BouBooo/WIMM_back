<?php

use App\Http\Controllers\Plaid\AccountController;
use App\Http\Controllers\Plaid\GraphController;
use App\Http\Controllers\Plaid\TokenAccessController;
use App\Http\Controllers\Plaid\TransactionController;
use App\Http\Controllers\ReminderController;
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
    'prefix' => 'auth',
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
    Route::resource('reminders', ReminderController::class)->except(['create', 'edit']);
});

Route::group([
    'middleware' => 'jwt.verify',
    'prefix' => 'plaid',
], static function ($router) {
    Route::post('/link-token/create', [TokenAccessController::class, 'createLinkToken']);
    Route::post('/public-token/exchange', [TokenAccessController::class, 'exchangePublicToken']);
    Route::get('/accounts', [AccountController::class, 'list']);
    Route::get('/accounts/identity', [AccountController::class, 'identity']);
    Route::get('/transactions', [TransactionController::class, 'list']);
    Route::get('/transactions/last', [TransactionController::class, 'last']);
    Route::get('/activity/graph', [GraphController::class, 'graphActivity']);
    Route::get('/balance/graph', [GraphController::class, 'graphBalance']);
});

Route::any('{any}', static function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Resource not found',
        'data' => [],
    ], Response::HTTP_NOT_FOUND);
})->where('any', '.*');
