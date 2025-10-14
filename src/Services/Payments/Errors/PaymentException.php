<?php

namespace App\Services\Payments\Errors;

use Exception;

class PaymentException extends Exception
{
    public function __construct(string $message = 'Payment processing error', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

