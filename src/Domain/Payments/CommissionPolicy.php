<?php

namespace App\Domain\Payments;

use App\Entities\Payment;
use App\PaymentMethods\Card;
use App\PaymentMethods\Qiwi;

/**
 * Политика уведомлений о комиссии
 * Уведомления отправляются если:
 * - Способ оплаты Qiwi
 * - Способ оплаты Card в валюте EUR
 */
class CommissionPolicy implements NotificationPolicy
{
    public function shouldNotify(Payment $payment): bool
    {
        $paymentMethod = $payment->getPaymentMethod();

        // Отправляем уведомление для Qiwi
        if ($paymentMethod instanceof Qiwi) {
            return true;
        }

        // Отправляем уведомление для Card в EUR
        if ($paymentMethod instanceof Card && $paymentMethod->getCurrency()->getCode() === 'EUR') {
            return true;
        }

        return false;
    }
}

