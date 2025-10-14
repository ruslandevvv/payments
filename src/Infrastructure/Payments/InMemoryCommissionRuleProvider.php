<?php

namespace App\Infrastructure\Payments;

use App\Banks\Sberbank;
use App\Banks\Tinkoff;
use App\Domain\Payments\CommissionRuleProvider;
use App\PaymentMethods\Card;
use App\PaymentMethods\Qiwi;
use App\Services\Payments\Commissions\CommissionRule;
use App\Services\Payments\Commissions\CommissionRules;
use Money\Money;

class InMemoryCommissionRuleProvider implements CommissionRuleProvider
{
    private CommissionRules $rules;

    public function __construct()
    {
        $this->rules = $this->initializeRules();
    }

    public function getRules(): CommissionRules
    {
        return $this->rules;
    }

    private function initializeRules(): CommissionRules
    {
        $rules = [];

        // Sberbank | Card | RUB | 1 — 1000 RUB | 4% | 1 RUB | 3 RUB
        $rules[] = new CommissionRule(
            Sberbank::NAME,
            Card::TYPE,
            'RUB',
            Money::RUB(100),
            Money::RUB(100000),
            4,
            Money::RUB(100),
            Money::RUB(300)
        );

        // Sberbank | Card | RUB | 1000 — 10000 RUB | 3% | 1 RUB | 3 RUB
        $rules[] = new CommissionRule(
            Sberbank::NAME,
            Card::TYPE,
            'RUB',
            Money::RUB(100100),
            Money::RUB(1000000),
            3,
            Money::RUB(100),
            Money::RUB(300)
        );

        // Sberbank | Card | EUR | 1 — 10000 EUR | 7% | 1 EUR | 4 EUR
        $rules[] = new CommissionRule(
            Sberbank::NAME,
            Card::TYPE,
            'EUR',
            Money::EUR(100),
            Money::EUR(1000000),
            7,
            Money::EUR(100),
            Money::EUR(400)
        );

        // Sberbank | Qiwi | RUB | 1 — 75000 RUB | 5% | 0 | 3 RUB
        $rules[] = new CommissionRule(
            Sberbank::NAME,
            Qiwi::TYPE,
            'RUB',
            Money::RUB(100),
            Money::RUB(7500000),
            5,
            Money::RUB(0),
            Money::RUB(300)
        );

        // Tinkoff | Card | RUB | от 15000 RUB | 2.5% | 0 | 0
        $rules[] = new CommissionRule(
            Tinkoff::NAME,
            Card::TYPE,
            'RUB',
            Money::RUB(1500000),
            Money::RUB(PHP_INT_MAX),
            2.5,
            Money::RUB(0),
            Money::RUB(0)
        );

        return new CommissionRules($rules);
    }
}

