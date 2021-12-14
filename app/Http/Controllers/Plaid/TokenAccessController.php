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
            return response()->json($validator->errors(), ResponseAlias::HTTP_BAD_REQUEST);
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
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }

        return response()->json([
            'message' => 'Plaid public token created',
            'linkToken' => $response->link_token,
        ], ResponseAlias::HTTP_OK);
    }

    public function exchangePublicToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
           'public_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), ResponseAlias::HTTP_BAD_REQUEST);
        }

        try {
            $response = $this->client->items->exchangeToken($validator->validated()['public_token']);
        } catch (PlaidRequestException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }

        return response()->json([
            'message' => 'Plaid access token created',
            'accessToken' => $response->access_token,
        ], ResponseAlias::HTTP_OK);
    }
}
