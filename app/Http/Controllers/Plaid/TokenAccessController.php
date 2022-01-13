<?php

namespace App\Http\Controllers\Plaid;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TomorrowIdeas\Plaid\Entities\User;
use TomorrowIdeas\Plaid\PlaidRequestException;

final class TokenAccessController extends AbstractPlaidController
{
    public function createLinkToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->getMessageBag()->first());
        }

        $locale = app()->getLocale();

        try {
            $response = $this->getClient()->tokens->create(
                'Sandbox Plaid Test App',
                $locale,
                [strtoupper($locale)],
                new User($validator->validated()['client_user_id']),
                ['auth'],
            );
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getMessage(), [], $e->getCode());
        }

        return $this->respond('Plaid public token created', [
            'linkToken' => $response->link_token
        ]);
    }

    public function exchangePublicToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
           'public_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->getMessageBag()->first());
        }

        try {
            $response = $this->getClient()->items->exchangeToken($validator->validated()['public_token']);
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getMessage(), [], $e->getCode());
        }

        $accessToken = $response->access_token;

        auth()->user()->update([
            'plaidAccessToken' => $accessToken,
            'hasBankSelected' => true, // Now, user has selected a bank.
        ]);

        return $this->respond('Plaid access token created', [
            'accessToken' => $accessToken
        ]);
    }
}
