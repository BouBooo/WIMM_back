<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function testRequiredFieldsForRegistration(): void
    {
        $this->jsonRequest(Request::METHOD_POST, '/auth/register')
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

        $this->jsonRequest(Request::METHOD_POST, '/auth/register', $userData)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                "message",
                "user" => ['firstName', 'lastName', 'email', 'created_at', 'updated_at', 'id'],
            ]);
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

        $this->jsonRequest(Request::METHOD_POST, '/auth/login', $userData)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "access_token",
                "token_type",
                "expires_in",
                "user" => ["id", "email", "firstName", "lastName", "email_verified_at", "created_at", "updated_at"]
            ]);
    }
}
