<?php

namespace App\Http\Controllers\Plaid;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use TomorrowIdeas\Plaid\Entities\User;
use TomorrowIdeas\Plaid\Plaid;
use TomorrowIdeas\Plaid\PlaidRequestException;

class TokenAccessController extends Controller
{
    private Plaid $client;

    public function __construct()
    {
        $this->client = new Plaid(
            config('services.plaid.client_id'),
            config('services.plaid.secret'),
            'sandbox'
        );
    }

    public function createLinkToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation errors', $validator->errors());
        }

        $locale = app()->getLocale();

        try {
            $response = $this->client->tokens->create(
                'Sandbox Plaid Test App',
                $locale,
                [strtoupper($locale)],
                new User($validator->validated()['client_user_id']),
                ['auth'],
            );
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getMessage(), null, $e->getCode());
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
            return $this->respondWithError('Validation errors', $validator->errors());
        }

        try {
            $response = $this->client->items->exchangeToken($validator->validated()['public_token']);
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getMessage(), null, $e->getCode());
        }

        return $this->respond('Plaid access token created', [
            'accessToken' => $response->access_token
        ]);
    }
}
