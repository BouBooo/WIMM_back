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
            Period::WEEK => $this->formatTransactions($data, $count, 'W'),
            Period::MONTH => $this->formatTransactions($data, $count, 'F'),
            Period::YEAR => $this->formatTransactions($data, $count, 'Y'),
        };
    }

    private function formatTransactions(array $transactions, int $count, string $format): array
    {
        $splited = [];

        foreach ($transactions as $transaction) {
            $date = Carbon::parse($transaction->date);
            $identifier = $date->format($format);
            $splited[$identifier] = [
                'label' => ($format === 'W')
                    ? 'Semaine du ' . $this->transactionService->getFirstDayOfTheWeek($date->year, $date->week)
                    : $identifier,
                'spent' => $this->transactionService->getSpentFromTransactions($transactions, $identifier, $format),
                'income' => $this->transactionService->getIncomeFromTransactions($transactions, $identifier, $format)
            ];
        }

        if (count($splited) > $count) {
            array_pop($splited);
        }

        return $this->harmonize($splited);
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
