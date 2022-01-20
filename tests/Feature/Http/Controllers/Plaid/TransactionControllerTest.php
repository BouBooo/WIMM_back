<?php

namespace Tests\Feature\Http\Controllers\Plaid;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    public function testRequiredStartDateField(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, '/plaid/transactions', [], true)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson(['status' => self::$error, 'message' => 'The start date field is required.', 'data' => []]);
    }

    public function testRequiredEndDateField(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, '/plaid/transactions?start_date=2021-12-10', [], true)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson(['status' => self::$error, 'message' => 'The end date field is required.', 'data' => []]);
    }

    public function testEndDateMustBeAfterStartDate(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, '/plaid/transactions?start_date=2021-12-10&end_date=2021-09-12&page=2', [], true)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson(['status' => self::$error, 'message' => 'end_date must be after start_date', 'data' => []]);
    }

    public function testSuccessfullyGetTransactions(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, '/plaid/transactions?start_date=2021-09-10&end_date=2021-12-12', [], true)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message', 'data']);
    }

    public function testSuccessfullyGettingPaginatedTransactions(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, '/plaid/transactions?start_date=2021-09-10&end_date=2021-12-12&page=2', [], true)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message', 'data']);
    }
}
