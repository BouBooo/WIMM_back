<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordRequestMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

final class ResetPasswordController extends Controller
{
    public function reset(Request $request): JsonResponse
    {
        $validator = Validator::make($request->only('email'), [
            'email' => "required|email",
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->errors()->first());
        }

        $email = $validator->validated()['email'];

        $user = User::where('email', $email)->first();
        if (null === $user) {
            return $this->respondWithError(sprintf('Unable to find user with email : %s', $email));
        }

        $token = Str::random(60);
        $user->update(['reset_password_token' => $token]);

        Mail::to($email)->queue(new ResetPasswordRequestMail($token));

        return $this->respond("Mail send successfully");
    }

    public function check(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'string|required',
            'email' => 'required|email',
            'password' => 'string|required|min:6',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->errors()->first());
        }

        $data = $validator->validated();

        $user = User::where([
            'email' => $data['email'],
            'reset_password_token' => $data['token'],
        ])->first();

        if (null === $user) {
            return $this->respondWithError('Invalid token or email');
        }

        $user->update([
            'password' => $data['password'],
            'reset_password_token' => null,
        ]);

        return $this->respond("Password reset successfully");
    }
}
