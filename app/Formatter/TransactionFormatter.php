<?php

namespace App\Formatter;

use App\Enums\Period;
use App\Services\TransactionService;
use Carbon\Carbon;

final class TransactionFormatter implements FormatterInterface
{
    public function __construct(
        private TransactionService $transactionService,
    ) {
    }

    public function format(array $data, string $mode, int $count): array
    {
        return match ($mode) {
            Period::DAY => $this->formatDay($data, $count),
            Period::WEEK => $this->formatWeek($data, $count),
            Period::MONTH => $this->formatMonth($data, $count),
            Period::YEAR => $this->formatYear($data, $count),
        };
    }

    private function formatDay(array $transactions, int $count): array
    {
        $daysData = $this->transactionService->initPeriodDays($count);

        foreach ($transactions as $transaction) {
            $date = $transaction->date ?? $transaction->authorized_date;
            $amount = $transaction->amount;

            if (isset($daysData[$date]) && $daysData[$date]['date'] === $date) {
                $daysData[$date]['spent'] += max($amount, 0);
                $daysData[$date]['income'] += min($amount, 0);
            }
        }

        return $this->harmonize($daysData);
    }

    private function formatWeek(array $transactions, int $count): array
    {
        $formattedWeekData = [];
        $weeks = array_chunk($transactions, $count); // Split by week.

        foreach ($weeks as $week) {
            $firstWeekDay = $week[0];
            $weekAmounts = array_map(static fn ($day) => $day->amount ?? 0, $week);

            $formattedWeekData[] = [
                'label' => 'Semaine du ' . Carbon::parse($firstWeekDay->date)->translatedFormat('d/m'),
                'spent' => array_sum(array_filter($weekAmounts, static fn ($amount) => $amount >= 0)),
                'income' => -1 * abs(array_sum(array_filter($weekAmounts, static fn ($amount) => $amount < 0))),
            ];
        }

        return $this->harmonize($formattedWeekData);
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

        return $this->harmonize($formattedMonthData);
    }

    private function formatYear(array $transactions, int $count): array
    {
        $formattedYearData = [];

        return $this->harmonize($formattedYearData);
    }

    private function harmonize(array $data): array
    {
        $formattedData = [];
        foreach ($data as $day) {
            $formattedData[] = [
                'label' => $day['label'],
                'spent' => $day['spent'],
                'income' => $day['income'],
            ];
        }

        return $formattedData;
    }
}
