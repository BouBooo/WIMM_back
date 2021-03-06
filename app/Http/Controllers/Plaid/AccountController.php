<?php

namespace App\Http\Controllers\Plaid;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TomorrowIdeas\Plaid\PlaidRequestException;

final class AccountController extends AbstractPlaidController
{
    public function list(Request $request): JsonResponse
    {
        $validator = Validator::make($request->only('account_ids'), [
            'account_ids' => 'present',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->getMessageBag()->first());
        }

        $accountIds = json_decode($validator->validated()['account_ids']);

        $options = [];
        if (!empty($accountIds)) {
            $options = ['account_ids' => $accountIds];
        }

        $plaidAccessToken = auth()->user()->plaidAccessToken;

        try {
            $response = $this->getClient()->accounts->list($plaidAccessToken, $options);
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getMessage(), [], $e->getCode());
        }

        $accountsPaginator = $this->paginate(
            $response->accounts,
            10,
            $request->query->getInt('page') + 1,
        );

        return $this->respond('Plaid Auth accounts', [
            'pagination' => [
                'current' => $accountsPaginator->currentPage() - 1,
                'total' => $accountsPaginator->total(),
            ],
            'accounts' => array_values($accountsPaginator->items()),
        ]);
    }

    public function identity(Request $request): JsonResponse
    {
        $validator = Validator::make($request->only('account_ids'), [
            'account_ids' => 'present',
        ]);

        $accountIds = json_decode($validator->validated()['account_ids']);

        $options = [];
        if (!empty($accountIds)) {
            $options = ['account_ids' => $accountIds];
        }

        $plaidAccessToken = auth()->user()->plaidAccessToken;

        try {
            $response = $this->getClient()->accounts->getIdentity($plaidAccessToken, $options);
        } catch (PlaidRequestException $e) {
            return $this->respondWithError($e->getResponse()?->error_message, [], $e->getCode());
        }

        $data = [];
        foreach ($response->accounts as $account) {
            $data[] = [
                'account_id' => $account->account_id,
                'owners' => $account->owners,
            ];
        }

        return $this->respond('Plaid accounts identity', [
            'accounts' => $data,
        ]);
    }

}
