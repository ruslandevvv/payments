<?php

namespace App\Services\Payments;

use App\Banks\Bank;
use App\Domain\Payments\CommissionRuleProvider;
use App\PaymentMethods\PaymentMethod;
use App\Services\Payments\Errors\PaymentException;

class BankResolver
{
    /** @var Bank[] */
    private array $banks;
    private CommissionRuleProvider $commissionRuleProvider;

    public function __construct(array $banks, CommissionRuleProvider $commissionRuleProvider)
    {
        $this->banks = $banks;
        $this->commissionRuleProvider = $commissionRuleProvider;
    }

    /**
     * Выбирает подходящий банк для проведения платежа
     *
     * @param PaymentMethod $paymentMethod
     * @param Money $amount
     * @return Bank
     * @throws PaymentException
     */
    public function resolve(PaymentMethod $paymentMethod, Money $amount): Bank
    {
        $rules = $this->commissionRuleProvider->getRules();

        foreach ($this->banks as $bank) {
            if (!$bank->supports($paymentMethod, $amount)) {
                continue;
            }

            // Дополнительно проверяем наличие подходящего правила комиссии
            $rule = $rules->findRule($bank->getName(), $paymentMethod->getType(), $amount);

            if ($rule !== null) {
                return $bank;
            }
        }

        throw new PaymentException(sprintf(
            'No bank available for payment method %s with amount %s %s',
            $paymentMethod->getType(),
            $amount->getAmount() / 100,
            $amount->getCurrency()->getCode()
        ));
    }

    /**
     * Возвращает все доступные банки
     *
     * @return Bank[]
     */
    public function getBanks(): array
    {
        return $this->banks;
    }
}

