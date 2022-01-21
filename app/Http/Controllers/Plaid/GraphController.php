<?php

namespace App\Http\Controllers\Plaid;

use App\Enums\Period;
use App\Formatter\TransactionFormatter;
use App\Services\TransactionService;
use Carbon\Carbon;
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
        if (Period::DAY === $period) {
            $count--;
        }

        $dates = $this->transactionService->getDatesByPeriod($period, $count);

        try {
            $response = $this->getClient()->transactions->list(
                auth()->user()->plaidAccessToken, $dates['startDate'], $dates['endDate'],
                [ 'count' => config('services.plaid.transactions.fetch_count') ]
            );
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getResponse()?->error_message, [], $e->getCode());
        }

        $transactions = $this->formatter->format($response->transactions, $period, $count);

        return $this->respond('Get activity graph', $transactions);
    }

    public function graphBalance(): JsonResponse
    {
        $plaidAccessToken = auth()->user()->plaidAccessToken;

        try {
            $response = $this->getClient()->accounts->list($plaidAccessToken);
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getResponse()?->error_message, [], $e->getCode());
        }

        $result = [];

        foreach ($response->accounts as $account) {
            $result[$account->account_id] = [
                'name' => $account->name,
                'balance' => $account->balances->current,
                'preview' => [],
            ];
        }

        $transactionsResponse = $this->getClient()->transactions->list(
            $plaidAccessToken,
            Carbon::today()->modify("-6 months"),
            Carbon::today(),
            ['account_ids' => array_keys($result)],
        );

        $transactions = $this->transactionService->splitByAccounts($transactionsResponse->transactions);

        foreach ($transactions as $account => $groupedTransactions) {
            $result[$account]['preview'] = $this->transactionService->splitByMonth($groupedTransactions);
        }

        return $this->respond('Get balance graph', array_values($result));
    }
}
