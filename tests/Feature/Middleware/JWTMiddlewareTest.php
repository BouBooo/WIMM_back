<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use JWTAuth;

class JWTMiddlewareTest extends TestCase
{
    public function testAuthorizationHeaderMissing(): void
    {
        $this->jsonRequest(Request::METHOD_GET, self::USER_PROFILE_ROUTE)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertExactJson([
                'status' => self::$error,
                'message' => 'Authorization Token not found',
                'data' => [],
            ]);
    }

    public function testInvalidToken(): void
    {
        $this->jsonRequest(Request::METHOD_GET, self::USER_PROFILE_ROUTE, [], [
            'Authorization' => 'Bearer ' . 'blablabla'
        ])
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'status' => self::$error,
                'message' => 'Token is Invalid',
                'data' => [],
            ]);
    }

    public function testSuccessfulJWTToken(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $this->jsonRequest(Request::METHOD_GET, self::USER_PROFILE_ROUTE, [], [
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(Response::HTTP_OK);
    }
}
