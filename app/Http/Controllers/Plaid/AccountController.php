<?php

namespace App\Http\Controllers\Plaid;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TomorrowIdeas\Plaid\PlaidRequestException;

class AccountController extends AbstractPlaidController
{
    public function authData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->only('account_ids'), [
            'account_ids' => ''
        ]);

        $user = auth()->user();

        try {
            $auth = $this->getClient()->auth->get($user->accessToken);
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getMessage(), null, $e->getCode());
        }
    }
}
