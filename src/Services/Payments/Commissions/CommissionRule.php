<?php

namespace App\Services\Payments\Commissions;

class CommissionRule
{
    private string $bankName;
    private string $paymentMethodType;
    private string $currencyCode;
    private Money $amountFrom;
    private Money $amountTo;
    private float $feePercent;
    private Money $feeFix;
    private Money $feeMin;

    public function __construct(
        string $bankName,
        string $paymentMethodType,
        string $currencyCode,
        Money $amountFrom,
        Money $amountTo,
        float $feePercent,
        Money $feeFix,
        Money $feeMin
    ) {
        $this->bankName = $bankName;
        $this->paymentMethodType = $paymentMethodType;
        $this->currencyCode = $currencyCode;
        $this->amountFrom = $amountFrom;
        $this->amountTo = $amountTo;
        $this->feePercent = $feePercent;
        $this->feeFix = $feeFix;
        $this->feeMin = $feeMin;
    }

    public function matches(string $bankName, string $paymentMethodType, Money $amount): bool
    {
        return $this->bankName === $bankName
            && $this->paymentMethodType === $paymentMethodType
            && $this->currencyCode === $amount->getCurrency()->getCode()
            && $amount->greaterThanOrEqual($this->amountFrom)
            && $amount->lessThanOrEqual($this->amountTo);
    }

    public function getFeePercent(): float
    {
        return $this->feePercent;
    }

    public function getFeeFix(): Money
    {
        return $this->feeFix;
    }

    public function getFeeMin(): Money
    {
        return $this->feeMin;
    }

    public function getBankName(): string
    {
        return $this->bankName;
    }

    public function getPaymentMethodType(): string
    {
        return $this->paymentMethodType;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }
}

