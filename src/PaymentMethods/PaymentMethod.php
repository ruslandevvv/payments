<?php

namespace App\PaymentMethods;

use Money\Currency;

interface PaymentMethod
{
    public function getType(): string;
    
    public function getCurrency(): Currency;
}

