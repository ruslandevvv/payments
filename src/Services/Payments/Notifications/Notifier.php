<?php

namespace App\Services\Payments\Notifications;

use App\Domain\Payments\NotificationPolicy;
use App\Entities\Payment;

class Notifier
{
    private NotificationPolicy $notificationPolicy;

    public function __construct(NotificationPolicy $notificationPolicy)
    {
        $this->notificationPolicy = $notificationPolicy;
    }

    /**
     * Отправляет уведомление о платеже, если это необходимо согласно политике
     *
     * @param Payment $payment
     * @return bool
     */
    public function notifyIfNeeded(Payment $payment): bool
    {
        if (!$this->notificationPolicy->shouldNotify($payment)) {
            return false;
        }

        $this->sendNotification($payment);

        return true;
    }

    /**
     * Отправляет уведомление (симуляция)
     *
     * @param Payment $payment
     */
    private function sendNotification(Payment $payment): void
    {
        // Симуляция отправки уведомления
        // В реальном приложении здесь был бы код отправки email, SMS, push-уведомления и т.д.
        
        $amount = $payment->getAmount()->getAmount();
        $currency = $payment->getAmount()->getCurrency()->getCode();
        $commission = $payment->getCommission()->getAmount();
        
        // Логируем для демонстрации
        error_log(
            sprintf(
                'Payment processed: Amount=%s %s, Commission=%s %s, Method=%s',
                $amount / 100,
                $currency,
                $commission / 100,
                $currency,
                $payment->getPaymentMethod()->getType()
            )
        );
    }
}

