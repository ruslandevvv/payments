<?php


namespace App\Entities;


use App\PaymentMethods\Card;
use DateTime;
use Money\Money;

class Payment
{

    private Money $amount;
    private Money $commission;
    private Card $card;
    private DateTime $createdAt;

    public function __construct(Money $amount, Money $commission, Card $card)
    {
        $this->amount = $amount;
        $this->commission = $commission;
        $this->card = $card;
        $this->createdAt = new DateTime();
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getCommission(): Money
    {
        return $this->commission;
    }

    public function getCard(): Card
    {
        return $this->card;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getNetAmount(): Money
    {
        return $this->amount->subtract($this->commission);
    }

}