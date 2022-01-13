<?php

namespace App\Exceptions;

use http\Exception\InvalidArgumentException;

final class InvalidPeriodException extends InvalidArgumentException
{
    protected $message = 'The provided period is not valid.';
}
