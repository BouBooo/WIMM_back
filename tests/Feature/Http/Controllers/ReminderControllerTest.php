<?php

namespace Http\Controllers;

use App\Models\Reminder;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ReminderControllerTest extends TestCase
{
    public function testCreateReminderRequiredField(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_POST, self::REMINDER_ROUTE, [
            'title' => 'My reminder',
            'start_date' => Carbon::now(),
        ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson(['status' => self::$error, 'message' => 'The end date field is required.', 'data' => []]);
    }

    public function testSuccessfullyCreateReminder(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_POST, self::REMINDER_ROUTE, [
            'title' => 'My reminder',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->modify('+2 hours'),
        ])->assertStatus(Response::HTTP_OK)->assertJsonStructure(['status', 'message', 'data']);
    }

    public function testSuccessfullyGetReminder(): void
    {
        $reminder = Reminder::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);

        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::REMINDER_ROUTE .'/' . $reminder->id)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message', 'data']);
    }

    public function testGetNonExistentReminder(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_GET, self::REMINDER_ROUTE .'/' . random_int(0, 999))
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson(['status' => self::$error, 'message' => 'Reminder not found', 'data' => []]);
    }

    public function testSuccessfullyUpdateReminder(): void
    {
        $reminder = Reminder::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);

        $this->makeAuthenticatedRequest(Request::METHOD_PATCH, self::REMINDER_ROUTE .'/' . $reminder->id, [
            'title' => 'My modified reminder',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->modify('+2 hours'),
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message', 'data']);
    }

    public function testUpdateNonExistentReminder(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_PATCH, self::REMINDER_ROUTE .'/' . random_int(0, 999), [
            'title' => 'Random title',
        ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson(['status' => self::$error, 'message' => 'Reminder not found', 'data' => []]);
    }

    public function testSuccessfullyDeleteReminder(): void
    {
        $reminder = Reminder::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);

        $this->makeAuthenticatedRequest(Request::METHOD_DELETE, self::REMINDER_ROUTE .'/' . $reminder->id, [])
            ->assertStatus(Response::HTTP_OK)->assertJsonStructure(['status', 'message', 'data']);
    }

    public function testDeleteNonExistentReminder(): void
    {
        $this->makeAuthenticatedRequest(Request::METHOD_DELETE, self::REMINDER_ROUTE .'/' . random_int(0, 999), [])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson(['status' => self::$error, 'message' => 'Reminder not found', 'data' => []]);
    }
}
