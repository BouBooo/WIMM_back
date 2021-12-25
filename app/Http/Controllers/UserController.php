<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify');
    }

    public function userProfile(): JsonResponse
    {
        return $this->respond('User data', [
            'user' => auth()->user()
        ]);
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
            return $this->respondWithError($validator->errors()->first());
        }

        $user->update($request->all());

        return $this->respond('User successfully updated', [
            'user' => $user->fresh()
        ]);
    }
}
