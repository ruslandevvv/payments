<?php

namespace App\Services\Payments\Errors;

class ProcessingException extends PaymentException
{
    public function __construct(string $message = 'Payment processing failed', ?\Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}

