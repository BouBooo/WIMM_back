<?php

namespace App\Formatter;

interface FormatterInterface
{
    /**
     * Format data depends on mode.
     */
    public function format(array $data, string $mode, int $count): array;
}
