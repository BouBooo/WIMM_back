<?php

namespace Tests\Feature\Controllers\Plaid;

use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TokenAccessControllerTest extends TestCase
{
    public function testRequiredCreateLinkTokenField(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_POST, self::CREATE_LINK_TOKEN_ROUTE)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson([
                'status' => self::$error,
                'message' => 'The client user id field is required.',
                'data' => [],
            ]);
    }

    public function testSuccessfulCreateLinkToken(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_POST, self::CREATE_LINK_TOKEN_ROUTE, [
            'client_user_id' => Uuid::uuid4()->__toString()
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message', 'data']);
    }
}
