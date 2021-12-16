<?php

namespace App\Http\Controllers\Plaid;

use App\Http\Controllers\Controller;
use TomorrowIdeas\Plaid\Plaid;

abstract class AbstractPlaidController extends Controller
{
    private Plaid $client;

    public function __construct()
    {
        $this->client = new Plaid(
            config('services.plaid.client_id'),
            config('services.plaid.secret'),
            'sandbox'
        );
    }

    public function getClient(): Plaid
    {
        return $this->client;
    }
}
