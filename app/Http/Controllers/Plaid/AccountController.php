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
            'account_ids' => 'present|array'
        ]);

        $accountIds = $validator->validated()['account_ids'];

        $options = [];
        if (!empty($accountIds)) {
            $options = ['account_ids' => $accountIds];
        }

        $plaidAccessToken = auth()->user()->plaidAccessToken;

        try {
            $auth = $this->getClient()->auth->get($plaidAccessToken, $options);
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getMessage(), [], $e->getCode());
        }

        return $this->respond('Plaid Auth accounts', [
            'accounts' => $auth->accounts,
        ]);
    }
}
