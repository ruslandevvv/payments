<?php

namespace App\Domain\Payments;

use App\Entities\Payment;

interface NotificationPolicy
{
    /**
     * Определяет, нужно ли отправлять уведомление для данного платежа
     *
     * @param Payment $payment
     * @return bool
     */
    public function shouldNotify(Payment $payment): bool;
}

