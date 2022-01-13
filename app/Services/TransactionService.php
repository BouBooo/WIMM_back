<?php

namespace App\Services;

use App\Enums\Period;
use App\Exceptions\InvalidPeriodException;

class TransactionService
{
    public function getDatesByPeriod(string $period, int $count): array
    {
        $nbrDays = match ($period) {
            Period::DAY => $count,
            Period::WEEK => 7 * $count,
            Period::MONTH => 31 * $count,
            Period::YEAR => 365 * $count,
            default => throw new InvalidPeriodException(),
        };

        return [
            'startDate' => (new \DateTime())->modify("-$nbrDays days"),
            'endDate' => new \DateTime(),
        ];
    }
}
