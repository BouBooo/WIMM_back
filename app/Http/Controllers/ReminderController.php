<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(): JsonResponse
    {
        return $this->respond('All user reminders', [
            'reminders' => auth()->user()->reminders()->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
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

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $reminder = Reminder::find($id);

        if (!$reminder) {
            return $this->respondWithError('Reminder not found');
        }

        return $this->respond('Reminder ', compact('reminder'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id$id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        // TODO
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
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
