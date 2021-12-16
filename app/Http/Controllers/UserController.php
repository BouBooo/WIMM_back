<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('jwt.verify');
    }

    public function userProfile(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    public function update(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|between:2,100',
            'lastName' => 'required|string|between:2,100',
            'email' => 'unique:users,email,'. $user->id .',id',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), ResponseAlias::HTTP_BAD_REQUEST);
        }

        $user->update($request->all());

        return response()->json([
            'message' => 'User successfully updated',
            'user' => $user->fresh()
        ], ResponseAlias::HTTP_OK);
    }
}
