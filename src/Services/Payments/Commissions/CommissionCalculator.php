<?php

namespace App\Services\Payments\Commissions;

use App\Services\Payments\Errors\PaymentException;

class CommissionCalculator
{
    /**
     * Рассчитывает комиссию по формуле: max(amount * fee_percent + fee_fix, fee_min)
     *
     * @param Money $amount Сумма платежа
     * @param CommissionRule $rule Правило комиссии
     * @return Money Рассчитанная комиссия
     * @throws PaymentException
     */
    public function calculate(Money $amount, CommissionRule $rule): Money
    {
        if (!$amount->isPositive()) {
            throw new PaymentException('Amount must be positive');
        }

        // Вычисляем процентную часть: amount * fee_percent
        $percentPart = $amount->multiply($rule->getFeePercent() / 100);

        // Добавляем фиксированную часть: amount * fee_percent + fee_fix
        $totalCommission = $percentPart->add($rule->getFeeFix());

        // Применяем правило минимальной комиссии: max(totalCommission, fee_min)
        if ($totalCommission->lessThan($rule->getFeeMin())) {
            return $rule->getFeeMin();
        }

        return $totalCommission;
    }
}

