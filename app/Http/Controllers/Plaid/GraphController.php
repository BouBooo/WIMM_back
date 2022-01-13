<?php

namespace App\Http\Controllers\Plaid;

use App\Formatter\TransactionFormatter;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TomorrowIdeas\Plaid\PlaidRequestException;

final class GraphController extends AbstractPlaidController
{
    public function __construct(
        private TransactionService $transactionService,
        private TransactionFormatter $formatter,
    ) {
        parent::__construct();
    }

    public function graphActivity(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'period' => 'in:day,week,month,year|required',
            'count' => 'int|required',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->getMessageBag()->first());
        }

        $data = $validator->validated();

        $period = $data['period'];
        $count = $data['count'];
        $dates = $this->transactionService->getDatesByPeriod($period, $count);

        try {
            $response = $this->getClient()->transactions->list(
                auth()->user()->plaidAccessToken, $dates['startDate'], $dates['endDate'],
            );
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getResponse()?->error_message, [], $e->getCode());
        }

        $transactions = $this->formatter->format($response->transactions, $period, $count);

        return $this->respond('Get activity graph', $transactions);
    }

    public function graphBalance(Request $request): JsonResponse
    {
        try {
            $response = $this->getClient()->accounts->list(
                auth()->user()->plaidAccessToken
            );
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getResponse()?->error_message, [], $e->getCode());
        }

        $result = [];

        foreach ($response->accounts as $account) {
            $result[] = [
                'name' => $account->name,
                'balance' => $account->balances->current,
                'preview' => [12350, 12500, 12457, 12627, 12527, 12700, 12827]
            ];
        }

        return $this->respond('Get balance graph', $result);
    }
}
