<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use JWTAuth;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private const REGISTER_ROUTE = "/auth/register";
    private const LOGIN_ROUTE = "/auth/login";

    public function testRequiredFieldsForRegistration(): void
    {
        $this->jsonRequest(Request::METHOD_POST, self::REGISTER_ROUTE)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson([
                "firstName" => ["The first name field is required."],
                "lastName" => ["The last name field is required."],
                "email" => ["The email field is required."],
                "password" => ["The password field is required."]
            ]);
    }

    public function testSuccessfulRegistration(): void
    {
        $userData = [
            "firstName" => "John",
            "lastName" => "Doe",
            "email" => "doe@example.com",
            "password" => "demo12345",
        ];

        $this->jsonRequest(Request::METHOD_POST, self::REGISTER_ROUTE, $userData)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                "message",
                "user" => ['firstName', 'lastName', 'email', 'created_at', 'updated_at', 'id'],
            ]);
    }

    public function testRequiredFieldsForLogin(): void
    {
        $this->jsonRequest(Request::METHOD_POST, self::LOGIN_ROUTE)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson([
                "email" => ["The email field is required."],
                "password" => ["The password field is required."],
            ]);
    }

    public function testLoginWithBadCredentials(): void
    {
        $user = User::factory()->create();

        $userData = [
            "email" => $user->email,
            "password" => $user->password,
        ];

        $this->jsonRequest(Request::METHOD_POST, self::LOGIN_ROUTE, $userData)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertExactJson(['error' => 'Unauthorized']);
    }

    public function testSuccessfulLogin(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password'),
        ]);

        $userData = [
            "email" => $user->email,
            "password" => $password,
        ];

        $this->jsonRequest(Request::METHOD_POST, self::LOGIN_ROUTE, $userData)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "access_token",
                "token_type",
                "expires_in",
                "user" => ["id", "email", "firstName", "lastName", "email_verified_at", "created_at", "updated_at"]
            ]);
    }

    public function testSuccessfulLogout(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $this->jsonRequest(Request::METHOD_POST, '/auth/logout', [], ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson(['message' => 'User successfully signed out']);
    }
}