<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    private const BASE_API_URL = "/api";

    use CreatesApplication;

    protected function jsonRequest(string $method, string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->json($method, self::BASE_API_URL . $uri, $data, [...$headers, 'Accept' => 'application/json']);
    }
}
