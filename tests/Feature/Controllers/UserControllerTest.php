<?php

namespace Tests\Feature\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function testSuccessfulGetUserProfile(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::USER_PROFILE_ROUTE)
            ->assertStatus(Response::HTTP_OK);
    }

    public function testSuccessfulUpdateUserProfile(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_POST, self::USER_PROFILE_ROUTE, [
            'firstName' => 'newFirstName',
        ])->assertStatus(Response::HTTP_OK);
    }
}
