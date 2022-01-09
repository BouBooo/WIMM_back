<?php

namespace App\Formatter;

use App\Enums\Period;
use Carbon\Carbon;

final class TransactionFormatter implements FormatterInterface
{
    public function format(array $data, string $mode, int $count): array
    {
        return match ($mode) {
            Period::DAY => $this->formatDay($data),
            Period::WEEK => $this->formatWeek($data, $count),
            Period::MONTH => $this->formatMonth($data, $count),
            Period::YEAR => $this->formatYear($data, $count),
        };
    }

    private function formatDay(array $transactions): array
    {
        $formattedDayData = [];

        foreach ($transactions as $transaction) {
            $formattedDayData[] = [
                'label' => Carbon::parse($transaction['authorized-date'])->format('D'),
                'spent' => max($transaction['amount'], 0),
                'income' => min($transaction['amount'], 0),
            ];
        }

        return $formattedDayData;
    }

    private function formatWeek(array $transactions, int $count): array
    {
        $formattedWeekData = [];
        $weeks = array_chunk($transactions, $count); // Split by week.

        foreach ($weeks as $week) {
            $firstWeekDay = array_reverse($week)[0];
            $weekAmounts = array_map(static fn ($day) => $day['amount'] ?? 0, $week);

            $formattedWeekData[] = [
                'label' => 'Semaine du ' . Carbon::parse($firstWeekDay['authorized-date'])->format('d/m'),
                'spent' => array_sum(array_filter($weekAmounts, static fn ($amount) => $amount >= 0)),
                'income' => -1 * abs(array_sum(array_filter($weekAmounts, static fn ($amount) => $amount < 0))),
            ];
        }

        return $formattedWeekData;
    }

    private function formatMonth(array $transactions, int $count): array
    {
        $formattedMonthData = [];

        return $formattedMonthData;
    }

    private function formatYear(array $transactions, int $count): array
    {
        $formattedYearData = [];

        return $formattedYearData;
    }
}
