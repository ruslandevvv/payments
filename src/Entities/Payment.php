<?php

namespace App\Entities;

use App\PaymentMethods\PaymentMethod;
use DateTime;
use Money\Money;

class Payment
{
    private Money $amount;
    private Money $commission;
    private PaymentMethod $paymentMethod;
    private DateTime $createdAt;
    private ?string $bankName;

    public function __construct(
        Money $amount,
        Money $commission,
        PaymentMethod $paymentMethod,
        ?string $bankName = null
    ) {
        $this->amount = $amount;
        $this->commission = $commission;
        $this->paymentMethod = $paymentMethod;
        $this->createdAt = new DateTime();
        $this->bankName = $bankName;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getCommission(): Money
    {
        return $this->commission;
    }

    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): void
    {
        $this->bankName = $bankName;
    }

    /**
     * Возвращает чистую сумму (сумма минус комиссия)
     * Это сумма, которую получит продавец
     */
    public function getNetAmount(): Money
    {
        return $this->amount->subtract($this->commission);
    }
}
