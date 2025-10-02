<?php

namespace App\Services\Payments;

use App\Entities\Payment;
use App\Services\Payments\Commands\CreatePaymentCommand;
use Money\Money;

class CreatePaymentService
{
    public function handle(CreatePaymentCommand $command): Payment
    {
        $commission = Money::RUB(100);
        return new Payment($command->getAmount(), $commission, $command->getCard());
    }
}