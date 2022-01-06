<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'refresh']]);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->getMessageBag()->first());
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return $this->respondWithError('Invalid credentials', [], ResponseAlias::HTTP_UNAUTHORIZED);
        }

        return $this->createNewToken($token);
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|between:2,100',
            'lastName' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->getMessageBag()->first());
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return $this->respond('User successfully registered', $user, ResponseAlias::HTTP_CREATED);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();

        return $this->respond('Successfully logged out');
    }

    public function refresh(): JsonResponse
    {
        return $this->createNewToken(auth()->refresh(), 'Token successfully refreshed');
    }

    private function createNewToken(string $token, string $message = 'Successfully logged in'): JsonResponse
    {
        return $this->respond($message, [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
