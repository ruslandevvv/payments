<?php

namespace App\Services\Payments;

use App\Banks\Sberbank;
use App\Entities\Payment;

class ChargePaymentService
{
    public function handle(Payment $payment): \App\Banks\Responses\Payment
    {
        $bank = new Sberbank();
        return $bank->createPayment($payment->getAmount(), $payment->getCard());
    }
}