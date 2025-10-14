<?php

namespace App\Banks;

use App\Banks\Responses\Payment;
use App\PaymentMethods\PaymentMethod;

interface Bank
{
    public function getName(): string;
    
    public function createPayment(Money $amount, PaymentMethod $paymentMethod): Payment;
    
    public function supports(PaymentMethod $paymentMethod, Money $amount): bool;
}

