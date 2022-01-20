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
                'label' => 'Semaine du ' . $this->transactionService->getFirstDayOfTheWeek($date->year, $date->week),
                'spent' => $this->transactionService->getSpentFromTransactions($transactions, $identifier, 'W'),
                'income' => $this->transactionService->getIncomeFromTransactions($transactions, $identifier, 'W')
            ];
        }

        if (count($weeksSplit) > $count) {
            array_pop($weeksSplit);
        }

        return $this->harmonize($weeksSplit);
    }

    private function formatMonth(array $transactions, int $count): array
    {
        $monthsSplit = [];

        foreach ($transactions as $transaction) {
            $date = Carbon::parse($transaction->date);
            $identifier = $date->format('F');
            $monthsSplit[$identifier] = [
                'label' => $identifier,
                'spent' => $this->transactionService->getSpentFromTransactions($transactions, $identifier, 'F'),
                'income' => $this->transactionService->getIncomeFromTransactions($transactions, $identifier, 'F')
            ];
        }

        if (count($monthsSplit) > $count) {
            array_pop($monthsSplit);
        }

        return $this->harmonize($monthsSplit);
    }

    private function formatYear(array $transactions, int $count): array
    {
        $yearsSplit = [];

        foreach ($transactions as $transaction) {
            $date = Carbon::parse($transaction->date);
            $identifier = $date->format('Y');
            $yearsSplit[$identifier] = [
                'label' => $identifier,
                'spent' => $this->transactionService->getSpentFromTransactions($transactions, $identifier, 'Y'),
                'income' => $this->transactionService->getIncomeFromTransactions($transactions, $identifier, 'Y')
            ];
        }

        if (count($yearsSplit) > $count) {
            array_pop($yearsSplit);
        }

        return $this->harmonize($yearsSplit);
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
