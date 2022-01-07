<?php

namespace App\Formatter;

class TransactionFormatter implements FormatterInterface
{
    public function format(array $data, string $mode): array
    {
        return [];
    }

    private function formatDay() {}

    private function formatWeek() {}

    private function formatMonth() {}

    private function formatYear() {}
}
