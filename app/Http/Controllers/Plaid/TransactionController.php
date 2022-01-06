<?php

namespace App\Http\Controllers\Plaid;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TomorrowIdeas\Plaid\PlaidRequestException;

class TransactionController extends AbstractPlaidController
{
    public function list(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'date_format:Y-m-d|required',
            'end_date' => 'date_format:Y-m-d|required',
            'options' => 'present|array',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->getMessageBag()->first());
        }

        $plaidAccessToken = auth()->user()->plaidAccessToken;

        $data = $validator->validated();

        $startDate = new \DateTime($data['start_date']);
        $endDate = new \DateTime($data['end_date']);
        $options = $data['options'];

        try {
            $response = $this->getClient()->transactions->list(
                $plaidAccessToken, $startDate, $endDate, $options,
            );
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getResponse()?->error_message, [], $e->getCode());
        }

        return $this->respond('Plaid transactions', [
            'transactions' => $response->transactions,
        ]);
    }
}
