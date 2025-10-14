<?php

namespace App\Services\Payments;

use App\Services\Payments\Commissions\CommissionCalculator;
use App\Services\Payments\Commissions\CommissionRule;
use Money\Money;

/**
 * Калькулятор для расчета финальной суммы платежа с учетом перекладывания комиссии на покупателя
 * 
 * Если продавец хочет получить A, а комиссия составляет B%, то покупатель должен заплатить такую сумму, чтобы после
 * вычета комиссии продавец получил A
 */
class BuyerGrossAmountCalculator
{
    private CommissionCalculator $commissionCalculator;
    private const MAX_ITERATIONS = 10;
    private const PRECISION = 1;

    public function __construct(CommissionCalculator $commissionCalculator)
    {
        $this->commissionCalculator = $commissionCalculator;
    }

    /**
     * Рассчитывает финальную сумму, которую должен заплатить покупатель,
     * чтобы продавец получил netAmount после вычета комиссии
     *
     * @param Money $netAmount Сумма, которую должен получить продавец (без комиссии)
     * @param CommissionRule $rule Правило комиссии
     * @return Money Сумма, которую должен заплатить покупатель (с комиссией)
     */
    public function calculate(Money $netAmount, CommissionRule $rule): Money
    {
        // Используем итеративный метод для нахождения gross amount
        // Начинаем с предположения, что gross = net + комиссия от net
        $currentGrossAmount = $netAmount->add(
            $this->commissionCalculator->calculate($netAmount, $rule)
        );

        // Итеративно уточняем, пока не достигнем нужной точности
        for ($i = 0; $i < self::MAX_ITERATIONS; $i++) {
            $commission = $this->commissionCalculator->calculate($currentGrossAmount, $rule);
            $calculatedNetAmount = $currentGrossAmount->subtract($commission);

            // Проверяем, достаточно ли близко мы к целевой net amount
            $difference = $netAmount->subtract($calculatedNetAmount);
            
            if (abs($difference->getAmount()) <= self::PRECISION) {
                // Достигли нужной точности
                return $currentGrossAmount;
            }

            // Корректируем gross amount на разницу
            $currentGrossAmount = $currentGrossAmount->add($difference);
        }

        // Если не сошлось за MAX_ITERATIONS, возвращаем последнее значение
        return $currentGrossAmount;
    }

    /**
     * Рассчитывает комиссию для переложенной на покупателя суммы
     *
     * @param Money $netAmount Сумма, которую должен получить продавец
     * @param CommissionRule $rule Правило комиссии
     * @return Money Комиссия
     */
    public function calculateCommission(Money $netAmount, CommissionRule $rule): Money
    {
        $grossAmount = $this->calculate($netAmount, $rule);

        return $this->commissionCalculator->calculate($grossAmount, $rule);
    }
}

