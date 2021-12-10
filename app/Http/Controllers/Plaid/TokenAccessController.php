<?php

namespace App\Http\Controllers\Plaid;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TokenAccessController extends Controller
{
    public function createAccessToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), ResponseAlias::HTTP_BAD_REQUEST);
        }

        $params = [
            'client_id' => config('services.plaid.client_id'),
            'secret' => config('services.plaid.secret'),
            'client_name' => 'Sandbox Plaid Test App',
            'language' => app()->getLocale(),
            'country_codes' => ['FR'],
            'user' => (object) ['client_user_id' => 'some-unique-client-id'],
            'products' => ['auth']
        ];

        $response = Http::post(config('services.plaid.create_access_token'), $params);

        return $response->json();
    }

    public function createPublicToken(Request $request)
    {
        // TODO
    }
}
