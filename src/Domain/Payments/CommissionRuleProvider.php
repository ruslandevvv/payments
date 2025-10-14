<?php

namespace App\Domain\Payments;

use App\Services\Payments\Commissions\CommissionRules;

interface CommissionRuleProvider
{
    public function getRules(): CommissionRules;
}

