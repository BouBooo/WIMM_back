<?php

namespace Tests\Feature;

use App\Http\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use JWTAuth;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, ApiResponse;

    private const REGISTER_ROUTE = "/auth/register";
    private const LOGIN_ROUTE = "/auth/login";

    private const DEFAULT_USER_ATTRIBUTES = ["id", "email", "firstName", "lastName", "email_verified_at", "hasBankSelected", "plaidAccessToken", "created_at", "updated_at"];
    private const CONNECTED_RESPONSE_ATTRIBUTES = ["access_token", "token_type", "expires_in", "user"];

    public function testRequiredFieldsForRegistration(): void
    {
        $this->jsonRequest(Request::METHOD_POST, self::REGISTER_ROUTE)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson([
                'status' => self::$error,
                "message" => "The first name field is required.",
                "data" => [],
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
            ->assertJsonStructure(["status", "message", "data" => ["id", "email", "firstName", "lastName", "created_at", "updated_at"]]);
    }

    public function testRequiredFieldsForLogin(): void
    {
        $this->jsonRequest(Request::METHOD_POST, self::LOGIN_ROUTE)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson([
                "status" => self::$error,
                "message" => "The email field is required.",
                "data" => [],
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
            ->assertExactJson(['status' => self::$error, 'message' => 'Invalid credentials', 'data' => []]);
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
            ->assertJsonStructure(["status", "message", "data" => self::CONNECTED_RESPONSE_ATTRIBUTES]);
    }

    public function testSuccessfulLogout(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $this->jsonRequest(Request::METHOD_POST, '/auth/logout', [], ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson(['status' => self::$success, 'message' => 'Successfully logged out', 'data' => []]);
    }

    public function testSuccessfulRefresh(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $this->jsonRequest(Request::METHOD_POST, '/auth/refresh', [], ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(["status", "message", "data" => self::CONNECTED_RESPONSE_ATTRIBUTES]);
    }
}
