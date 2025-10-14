<?php

namespace App\Services\Payments\Commissions;

class CommissionRules
{
    /** @var CommissionRule[] */
    private array $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function findRule(string $bankName, string $paymentMethodType, Money $amount): ?CommissionRule
    {
        foreach ($this->rules as $rule) {
            if ($rule->matches($bankName, $paymentMethodType, $amount)) {
                return $rule;
            }
        }

        return null;
    }

    public function addRule(CommissionRule $rule): void
    {
        $this->rules[] = $rule;
    }

    /**
     * @return CommissionRule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}

