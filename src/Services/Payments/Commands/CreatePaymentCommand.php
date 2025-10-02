<?php


namespace App\Services\Payments\Commands;


use App\PaymentMethods\Card;
use Money\Money;

class CreatePaymentCommand
{

    private Money $amount;
    private Card $card;

    public function __construct(Money $amount, Card $card)
    {
        $this->amount = $amount;
        $this->card = $card;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getCard(): Card
    {
        return $this->card;
    }
}