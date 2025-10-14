<?php

namespace App\Services\Payments;

use App\Domain\Payments\CommissionRuleProvider;
use App\Entities\Payment;
use App\Services\Payments\Commands\CreatePaymentCommand;
use App\Services\Payments\Commissions\CommissionCalculator;
use App\Services\Payments\Errors\PaymentException;
use Money\Money;

class CreatePaymentService
{
    private CommissionRuleProvider $commissionRuleProvider;
    private CommissionCalculator $commissionCalculator;
    private BuyerGrossAmountCalculator $grossAmountCalculator;
    private BankResolver $bankResolver;

    public function __construct(
        CommissionRuleProvider $commissionRuleProvider,
        CommissionCalculator $commissionCalculator,
        BuyerGrossAmountCalculator $grossAmountCalculator,
        BankResolver $bankResolver
    ) {
        $this->commissionRuleProvider = $commissionRuleProvider;
        $this->commissionCalculator = $commissionCalculator;
        $this->grossAmountCalculator = $grossAmountCalculator;
        $this->bankResolver = $bankResolver;
    }

    /**
     * Создает платеж с расчетом комиссии
     *
     * @param CreatePaymentCommand $command
     * @return Payment
     * @throws PaymentException
     */
    public function handle(CreatePaymentCommand $command): Payment
    {
        $amount = $command->getAmount();
        $paymentMethod = $command->getPaymentMethod();

        // Определяем банк для платежа
        $bank = $this->bankResolver->resolve($paymentMethod, $amount);

        // Находим правило комиссии
        $rules = $this->commissionRuleProvider->getRules();
        $rule = $rules->findRule($bank->getName(), $paymentMethod->getType(), $amount);

        if ($rule === null) {
            throw new PaymentException(sprintf(
                'No commission rule found for bank=%s, method=%s, currency=%s',
                $bank->getName(),
                $paymentMethod->getType(),
                $amount->getCurrency()->getCode()
            ));
        }

        // Рассчитываем комиссию в зависимости от того, кто ее платит
        if ($command->isChargeCommissionToSeller()) {
            // Комиссия взимается с продавца
            // Покупатель платит $amount, продавец получает $amount - $commission
            $commission = $this->commissionCalculator->calculate($amount, $rule);
            $paymentAmount = $amount;
        } else {
            // Комиссия взимается с покупателя
            // Необходимо выбрать такое правило, для которого gross действительно
            // попадает в диапазон сумм этого правила.

            // Это то, что должен получить продавец
            $netAmount = $amount;

            $selectedRule = null;
            $calculatedGross = null;

            foreach ($rules->getRules() as $candidate) {
                if (
                    $candidate->getBankName() !== $bank->getName()
                    && $candidate->getPaymentMethodType() !== $paymentMethod->getType()
                    && $candidate->getCurrencyCode() !== $netAmount->getCurrency()->getCode()
                ) {
                    continue;
                }

                $gross = $this->grossAmountCalculator->calculate($netAmount, $candidate);

                // Проверяем соответствие правила рассчитанному gross
                if ($candidate->matches($bank->getName(), $paymentMethod->getType(), $gross)) {
                    $selectedRule = $candidate;
                    $calculatedGross = $gross;

                    break;
                }
            }

            if ($selectedRule === null || $calculatedGross === null) {
                throw new PaymentException('Unable to find consistent commission rule for buyer-paid commission');
            }

            $paymentAmount = $calculatedGross;
            $commission = $this->grossAmountCalculator->calculateCommission($netAmount, $selectedRule);
        }

        return new Payment($paymentAmount, $commission, $paymentMethod, $bank->getName());
    }
}
