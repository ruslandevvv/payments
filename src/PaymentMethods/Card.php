<?php

namespace App\PaymentMethods;

use DateTime;
use Money\Currency;

class Card implements PaymentMethod
{
    public const TYPE = 'card';

    private string $pan;
    private DateTime $expiryDate;
    private int $cvc;
    private Currency $currency;

    public function __construct(string $pan, DateTime $expiryDate, int $cvc, Currency $currency)
    {
        $this->pan = $pan;
        $this->expiryDate = $expiryDate;
        $this->cvc = $cvc;
        $this->currency = $currency;
    }

    public function getPan(): string
    {
        return $this->pan;
    }

    public function getExpiryDate(): DateTime
    {
        return $this->expiryDate;
    }

    public function getCvc(): int
    {
        return $this->cvc;
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