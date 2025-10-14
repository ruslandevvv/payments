<?php

namespace App\Services\Payments\Commands;

use App\PaymentMethods\PaymentMethod;
use Money\Money;

class CreatePaymentCommand
{
    private Money $amount;
    private PaymentMethod $paymentMethod;
    private bool $chargeCommissionToSeller;

    /**
     * @param Money $amount Сумма платежа
     * @param PaymentMethod $paymentMethod Способ оплаты
     * @param bool $chargeCommissionToSeller true - комиссия с продавца, false - комиссия с покупателя
     */
    public function __construct(
        Money $amount,
        PaymentMethod $paymentMethod,
        bool $chargeCommissionToSeller = true
    ) {
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
        $this->chargeCommissionToSeller = $chargeCommissionToSeller;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function isChargeCommissionToSeller(): bool
    {
        return $this->chargeCommissionToSeller;
    }
}
