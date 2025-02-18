<?php

namespace App\Service;

use App\Entity\ProductType;
use App\PaymentRule\RuleType;

class ProductPaymentRuleResolver
{
    public function resolvePaymentRule(ProductType $productType, \DateTimeImmutable $soldDate): string
    {
        if ('6' === $soldDate->format('n')) {
            return RuleType::JUNE_RULE->value;
        }

        return $productType->getDefaultPaymentRule();
    }
}
