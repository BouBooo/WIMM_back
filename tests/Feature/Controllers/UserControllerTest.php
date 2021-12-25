<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use JWTAuth;

class UserControllerTest extends TestCase
{
    public function testSuccessfulGetUserProfile(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $this->jsonRequest(Request::METHOD_GET, self::USER_PROFILE_ROUTE, [], [
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(Response::HTTP_OK);
    }

    public function testSuccessfulUpdateUserProfile(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $this->jsonRequest(Request::METHOD_POST, self::USER_PROFILE_ROUTE,
            ['firstName' => 'newFirstName'],
            ['Authorization' => 'Bearer ' . $token]
        )->assertStatus(Response::HTTP_OK);
    }
}
