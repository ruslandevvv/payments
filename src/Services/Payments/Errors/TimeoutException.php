<?php

namespace App\Services\Payments\Errors;

class TimeoutException extends PaymentException
{
    public function __construct
    (
        string $message = 'Connection timeout',
        ?\Exception $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }
}

