<?php

namespace App\Http\Controllers\Plaid;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TomorrowIdeas\Plaid\PlaidRequestException;

final class TransactionController extends AbstractPlaidController
{
    public function list(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'date_format:Y-m-d|required',
            'end_date' => 'date_format:Y-m-d|required',
            'options' => 'array',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->getMessageBag()->first());
        }

        $plaidAccessToken = auth()->user()->plaidAccessToken;

        $data = $validator->validated();

        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $options = [];
        if (array_key_exists('options', $data)) {
            $options = $data['options'];
        }

        try {
            $response = $this->getClient()->transactions->list(
                $plaidAccessToken, $startDate, $endDate, $options,
            );
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getResponse()?->error_message, [], $e->getCode());
        }

        $transactionsPaginator = $this->paginate(
            $response->transactions,
            10,
            $request->query->getInt('page') + 1,
        );

        return $this->respond('Plaid transactions', [
            'pagination' => [
                'current' => $transactionsPaginator->currentPage() - 1,
                'total' => $transactionsPaginator->total(),
            ],
            'transactions' => array_values($transactionsPaginator->items()),
        ]);
    }

    public function last(): JsonResponse
    {
        $plaidAccessToken = auth()->user()->plaidAccessToken;
        $startDate = Carbon::today()->modify('-20 days');
        $endDate = Carbon::today();

        try {
            $response = $this->getClient()->transactions->list(
                $plaidAccessToken, $startDate, $endDate, ['count' => 4],
            );
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getResponse()?->error_message, [], $e->getCode());
        }

        $transactions = [];
        foreach ($response->transactions as $transaction) {
            $transactions[] = [
                "name" => $transaction->merchant_name ?? $transaction->name,
                "date" => Carbon::parse($transaction->date)->translatedFormat("d F Y"),
                "price" => -1 * abs($transaction->amount),
            ];
        }

        return $this->respond('Plaid transactions', [
            'transactions' => $transactions,
        ]);
    }
}
