<?php

namespace Tests;

use App\Http\Traits\ApiResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    private const BASE_API_URL = "/api";

    protected const REGISTER_ROUTE = "/auth/register";
    protected const LOGIN_ROUTE = "/auth/login";
    protected const USER_PROFILE_ROUTE = '/user-profile';

    use CreatesApplication, RefreshDatabase, ApiResponse;

    protected function jsonRequest(string $method, string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->json($method, self::BASE_API_URL . $uri, $data, $headers);
    }
}
