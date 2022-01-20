<?php

namespace Tests\Feature\Http\Controllers\Plaid;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    public function testSuccessfullyGetAccounts(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::ACCOUNTS_ROUTE . '?account_ids=[]&page=0', [], true)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message', 'data']);
    }

    public function testSuccessfullyGettingPaginatedAccounts(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::ACCOUNTS_ROUTE . '?account_ids=[]&page=2', [], true)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message', 'data']);
    }

    public function testSuccessfullyGettingSpecificAccount(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::ACCOUNTS_ROUTE . '?account_ids=["Gp1EZxmolqFKnlBNJAzki5rPMxBBoyi1AlvLA"]&page=0', [], true)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message', 'data']);
    }

    public function testSuccessfullyGettingSpecificAccounts(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::ACCOUNTS_ROUTE . '?account_ids=["Gp1EZxmolqFKnlBNJAzki5rPMxBBoyi1AlvLA", "nKW3EQjwkPHGL4mxdpXBFKpEe4qql7s6KdRXG"]&page=0', [], true)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message', 'data']);
    }

    public function testUnsuccessfullyGettingAccounts(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::ACCOUNTS_ROUTE)
            ->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test500GettingAccounts(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::ACCOUNTS_ROUTE . '?account_ids=[]&page=1')
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testSuccessfullyGetIdentity(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::ACCOUNTS_ROUTE . '/identity?account_ids=[]', [], true)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message', 'data']);
    }
}
