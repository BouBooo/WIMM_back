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
            'startDate' => Carbon::today()->modify("-$nbrDays days"),
            'endDate' => Carbon::today(),
        ];
    }

    public function initPeriodDays(int $count): array
    {
        $days = [];
        $period = CarbonPeriod::create(
            Carbon::today()->modify("-$count days")->format("Y-m-d"),
            Carbon::today()->format("Y-m-d")
        )->toArray();

        $moreThanOneWeek = count($period) > 7;

        foreach (array_reverse($period) as $date) {
            $formattedDate = $date->format("Y-m-d");
            $parsedDate = Carbon::parse($date);

            $days[$formattedDate] = [
                'date' => $formattedDate,
                'label' => $moreThanOneWeek ? $parsedDate->translatedFormat('d/m') : ucfirst($parsedDate->translatedFormat('l')),
                'spent' => 0,
                'income' => 0,
            ];
        }

        return $days;
    }

    public function splitByMonth(array $data): array
    {
        $result = [];
        $monthsSplited = [];

        foreach ($data as $transaction) {
            $monthsSplited[Carbon::parse($transaction->date)->format('F')][] = $transaction->amount;
        }

        foreach ($monthsSplited as $month => $amounts) {
            $result[$month] = round(array_sum($amounts), 2);
        }

        return array_values($result);
    }

    public function splitByAccounts(array $data): array
    {
        $result = [];

        foreach ($data as $transaction) {
            $result[$transaction->account_id][] = $transaction;
        }

        return $result;
    }

    public function getSpentFromTransactions($transactions, $identifier, $format): float|int
    {
        $spent = [];

        foreach ($transactions as $transaction) {
            if ($transaction->amount > 0 && $identifier === Carbon::parse($transaction->date)->format($format)) {
                $spent[] = abs($transaction->amount);
            }
        }

        return array_sum($spent);
    }

    public function getIncomeFromTransactions($transactions, $identifier, $format): float|int
    {
        $income = [];

        foreach ($transactions as $transaction) {
            if ($transaction->amount < 0 && $identifier === Carbon::parse($transaction->date)->format($format)) {
                $income[] = $transaction->amount;
            }
        }

        return array_sum($income);
    }

    public function getFirstDayOfTheWeek(int $year, int $week): string
    {
        return Carbon::now()->setISODate($year, $week)->format('d/m');
    }
}
