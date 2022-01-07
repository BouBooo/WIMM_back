<?php

namespace App\Services;

class TransactionService
{
    public function getDatesByPeriod(string $period, int $count): array
    {
        return ['startDate' => new \DateTime(), 'endDate' => new \DateTime()];
    }
}
