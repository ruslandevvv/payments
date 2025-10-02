<?php


namespace App\PaymentMethods;


use DateTime;

class Card
{

    private string $pan;
    private DateTime $expiryDate;
    private int $cvc;

    public function __construct(string $pan, DateTime $expiryDate, int $cvc)
    {
        $this->pan = $pan;
        $this->expiryDate = $expiryDate;
        $this->cvc = $cvc;
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
}