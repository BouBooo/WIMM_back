<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

final class ReminderController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->respond('All user reminders', [
            'reminders' => auth()->user()->reminders()->get()
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:2,100',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->getMessageBag()->first());
        }

        $reminder = Reminder::create([
            'title' => $request->get('title'),
            'start_date' => Carbon::create($request->get('start_date')),
            'end_date' => Carbon::create($request->get('end_date')),
            'user_id' => auth()->id()
        ]);

        return $this->respond('Reminder successfully created', compact('reminder'));
    }

    public function show(int $id): JsonResponse
    {
        $reminder = Reminder::find($id);

        if (!$reminder) {
            return $this->respondWithError('Reminder not found');
        }

        return $this->respond('Reminder ', compact('reminder'));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $reminder = Reminder::find($id);

        if (!$reminder) {
            return $this->respondWithError('Reminder not found');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|between:2,100',
            'start_date' => 'date_format:Y-m-d',
            'end_date' => 'date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->errors()->first());
        }

        $reminder->update($request->all());

        return $this->respond('Reminder successfully updated', [
            'reminder' => $reminder->fresh()
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $reminder = Reminder::find($id);

        if (!$reminder) {
            return $this->respondWithError('Reminder not found');
        }

        $reminder->delete();

        return $this->respond('Reminder deleted');
    }
}
