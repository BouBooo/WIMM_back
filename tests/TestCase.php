<?php

namespace Tests;

use App\Http\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase, ApiResponse;

    private const BASE_API_URL = "/api";
    private const TEST_PLAID_ACCESS_TOKEN = "access-sandbox-bf154942-35f3-4c60-bab4-2b2f75f6a71b";

    protected const REGISTER_ROUTE = "/auth/register";
    protected const LOGIN_ROUTE = "/auth/login";
    protected const USER_PROFILE_ROUTE = '/user-profile';
    protected const CREATE_LINK_TOKEN_ROUTE = '/plaid/link-token/create';
    protected const EXCHANGE_LINK_TOKEN_ROUTE = '/plaid/public-token/exchange';

    protected function jsonRequest(string $method, string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->json($method, self::BASE_API_URL . $uri, $data, $headers);
    }

    protected function makeAuthenticatedRequest(string $method, string $uri, array $data = [], bool $forPlaid = false): TestResponse
    {
        $attributes = [];
        if ($forPlaid) {
            $attributes = [
                'plaidAccessToken' => self::TEST_PLAID_ACCESS_TOKEN,
                'hasBankSelected' => true,
            ];
        }

        $user = User::factory()->create($attributes);
        $token = JWTAuth::fromUser($user);

        return $this->jsonRequest($method, $uri, $data, [
            'Authorization' => 'Bearer ' . $token
        ]);
    }
}
