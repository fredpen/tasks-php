<?php

namespace App\Exceptions;

use Exception;

class CustomError extends Exception
{
    public static function throw(string $message)
    {
        return new static($message);
    }
}
