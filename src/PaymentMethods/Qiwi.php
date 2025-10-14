<?php

namespace App\PaymentMethods;

use Money\Currency;

class Qiwi implements PaymentMethod
{
    public const TYPE = 'qiwi';

    private string $phoneNumber;
    private Currency $currency;

    public function __construct(string $phoneNumber, Currency $currency)
    {
        $this->phoneNumber = $phoneNumber;
        $this->currency = $currency;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}

