<?php

namespace App\Banks;

use App\Banks\Responses\Payment;
use App\PaymentMethods\Card;
use App\PaymentMethods\PaymentMethod;
use App\Services\Payments\Errors\HttpErrorException;
use App\Services\Payments\Errors\TimeoutException;
use Money\Money;

class Tinkoff implements Bank
{
    public const NAME = 'tinkoff';
    private const MIN_AMOUNT_RUB_MINOR = 1500000;

    public function getName(): string
    {
        return self::NAME;
    }

    public function createPayment(Money $amount, PaymentMethod $paymentMethod): Payment
    {
        // Симуляция возможных ошибок (для демонстрации обработки ошибок)
        $random = rand(1, 100);

        if ($random <= 5) {
            throw new HttpErrorException('Bank API returned HTTP 500', 500);
        }

        if ($random > 95) {
            throw new TimeoutException('Connection to bank timed out');
        }

        return new Payment(Payment::STATUS_COMPLETED);
    }

    public function supports(PaymentMethod $paymentMethod, Money $amount): bool
    {
        // Tinkoff поддерживает только карты, только RUB, и только от 15000 руб
        if (!$paymentMethod instanceof Card) {
            return false;
        }

        if ($paymentMethod->getCurrency()->getCode() !== 'RUB') {
            return false;
        }

        $minAmount = Money::RUB(self::MIN_AMOUNT_RUB_MINOR);
        
        return $amount->greaterThanOrEqual($minAmount);
    }
}

