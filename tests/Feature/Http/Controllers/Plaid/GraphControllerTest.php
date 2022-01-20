<?php

namespace Tests\Feature\Http\Controllers\Plaid;

use App\Enums\Period;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GraphControllerTest extends TestCase
{
    public function testSuccessfullyGetGraphBalance(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::BALANCE_GRAPH_ROUTE, [], true)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message', 'data']);
    }

    public function testBadPeriodProvided(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::ACTIVITY_GRAPH_ROUTE . '?period=invalid_period&count=4', [], true)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson(['status' => self::$error, 'message' => 'The selected period is invalid.', 'data' => []]);
    }

    public function testSuccessfullyGetGraphActivityInDays(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::ACTIVITY_GRAPH_ROUTE . '?period='.Period::DAY.'&count=4', [], true)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message', 'data']);
    }

    public function testSuccessfullyGetGraphActivityInWeeks(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::ACTIVITY_GRAPH_ROUTE . '?period='.Period::WEEK.'&count=2', [], true)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message', 'data']);
    }

    public function testSuccessfullyGetGraphActivityInMonths(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::ACTIVITY_GRAPH_ROUTE . '?period='.Period::MONTH.'&count=6', [], true)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message', 'data']);
    }

    public function testSuccessfullyGetGraphActivityInYears(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::ACTIVITY_GRAPH_ROUTE . '?period='.Period::YEAR.'&count=1', [], true)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message', 'data']);
    }
}
