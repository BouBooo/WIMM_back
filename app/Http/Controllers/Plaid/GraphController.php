<?php

namespace App\Http\Controllers\Plaid;

use App\Formatter\TransactionFormatter;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TomorrowIdeas\Plaid\PlaidRequestException;

class GraphController extends AbstractPlaidController
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
        $plaidAccessToken = auth()->user()->plaidAccessToken;

        try {
            $response = $this->getClient()->transactions->list(
                $plaidAccessToken, $dates['startDate'], $dates['endDate'],
            );
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getResponse()?->error_message, [], $e->getCode());
        }

        $transactions = $this->formatter->format($response->transactions, $period);

        return $this->respond('Get activity graph', [
            'transactions' => $transactions,
        ]);
    }
}
