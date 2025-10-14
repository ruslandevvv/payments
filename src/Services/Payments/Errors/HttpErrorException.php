<?php

namespace App\Services\Payments\Errors;

class HttpErrorException extends PaymentException
{
    private int $httpCode;

    public function __construct
    (
        string $message = 'HTTP error from bank',
        int $httpCode = 500,
        ?\Exception $previous = null
    ) {
        $this->httpCode = $httpCode;
        parent::__construct($message, $httpCode, $previous);
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}

