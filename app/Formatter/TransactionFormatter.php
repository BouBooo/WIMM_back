<?php

namespace App\Formatter;

use App\Enums\Period;
use App\Services\TransactionService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;

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
        $weeksSplit = [];

        foreach ($transactions as $transaction) {
            $date = Carbon::parse($transaction->date);
            $identifier = $date->format('W');
            $weeksSplit[$identifier] = [
                'label' => 'Semaine du ' . $this->getFirstDayOfTheWeek($date->year, $date->week),
                'spent' => $this->getSpentFromTransactions($transactions, $identifier),
                'income' => $this->getIncomeFromTransactions($transactions, $identifier)
            ];
        }

        if (count($weeksSplit) > $count) {
            array_pop($weeksSplit);
        }

        return $weeksSplit;
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

    private function getSpentFromTransactions($transactions, $identifier): float|int
    {
        $spent = [];

        foreach ($transactions as $transaction) {
            if (
                $identifier === Carbon::parse($transaction->date)->format('W')
                && $transaction->amount < 0
            ) {
                array_push($spent, abs($transaction->amount));
            }
        }

        return array_sum($spent);
    }

    private function getIncomeFromTransactions($transactions, $identifier): float|int
    {
        $income = [];

        foreach ($transactions as $transaction) {
            if (
                $identifier === Carbon::parse($transaction->date)->format('W')
                && $transaction->amount > 0
            ) {
                array_push($income, $transaction->amount);
            }
        }

        return array_sum($income);
    }

    private function getFirstDayOfTheWeek($year, $week): string
    {
        return Carbon::now()->setISODate($year, $week)->format('d/m');
    }
}
