<?php

namespace App\Enums;

abstract class Period
{
    public const DAY = 'day';
    public const WEEK = 'week';
    public const MONTH = 'month';
    public const YEAR = 'year';

    public const ALL = [
        self::DAY, self::WEEK ,self::MONTH, self::YEAR,
    ];
}
