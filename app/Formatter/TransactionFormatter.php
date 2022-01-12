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
                'label' => Carbon::parse($transaction->date ?? $transaction->authorized_date),
                'spent' => max($transaction->amount, 0),
                'income' => min($transaction->amount, 0),
            ];
        }

        return $formattedDayData;
    }

    private function formatWeek(array $transactions, int $count): array
    {
        $formattedWeekData = [];
        $weeks = array_chunk($transactions, $count); // Split by week.

        foreach ($weeks as $week) {
            $firstWeekDay = $week[0];
            $weekAmounts = array_map(static fn ($day) => $day->amount ?? 0, $week);

            $formattedWeekData[] = [
                'label' => 'Semaine du ' . Carbon::parse($firstWeekDay->date ?? $firstWeekDay->authorized_date)->translatedFormat('d/m'),
                'spent' => array_sum(array_filter($weekAmounts, static fn ($amount) => $amount >= 0)),
                'income' => -1 * abs(array_sum(array_filter($weekAmounts, static fn ($amount) => $amount < 0))),
            ];
        }

        return $formattedWeekData;
    }

    private function formatMonth(array $transactions, int $count): array
    {
        $formattedMonthData = [];

        $months = array_chunk($transactions, count($transactions) - $count); // Split by month.

        foreach ($months as $month) {
            $firstWeekDay = array_reverse($month)[0];
            $monthAmounts = array_map(static fn ($day) => $day->amount ?? 0, $month);

            $formattedMonthData[] = [
                'label' => ucfirst(Carbon::parse($firstWeekDay->date ?? $firstWeekDay->authorized_date)->translatedFormat('F')),
                'spent' => array_sum(array_filter($monthAmounts, static fn ($amount) => $amount >= 0)),
                'income' => -1 * abs(array_sum(array_filter($monthAmounts, static fn ($amount) => $amount < 0))),
            ];
        }

        return $formattedMonthData;
    }

    private function formatYear(array $transactions, int $count): array
    {
        $formattedYearData = [];

        return $formattedYearData;
    }
}
