<?php

namespace App\Services;

use App\Enums\Period;
use App\Exceptions\InvalidPeriodException;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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

    public function initPeriodDays(int $count): array
    {
        $days = [];
        $period = CarbonPeriod::create(
            (new \DateTime())->modify("-$count days")->format("Y-m-d"),
            (new \DateTime())->format("Y-m-d")
        )->toArray();

        foreach (array_reverse($period) as $date) {
            $formattedDate = $date->format("Y-m-d");

            $days[$formattedDate] = [
                'date' => $formattedDate,
                'label' => ucfirst(Carbon::parse($date)->translatedFormat('l')),
                'spent' => 0,
                'income' => 0,
            ];
        }

        return $days;
    }
}
