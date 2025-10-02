<?php


namespace App\Banks;


use App\Banks\Responses\Payment;
use App\PaymentMethods\Card;
use Money\Money;

class Sberbank
{
    public function createPayment(Money $amount, Card $card): Payment
    {
        return new Payment(Payment::STATUS_COMPLETED);
    }
}