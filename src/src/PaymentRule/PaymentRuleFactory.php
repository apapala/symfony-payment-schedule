<?php

namespace App\PaymentRule;

class PaymentRuleFactory
{
    public function __construct(private iterable $rules)
    {
    }

    public function getRule(string $ruleCode): PaymentRuleInterface
    {
        foreach ($this->rules as $rule) {
            if ($rule->supports($ruleCode)) {
                return $rule;
            }
        }

        throw new \InvalidArgumentException(sprintf('Payment rule "%s" not found', $ruleCode));
    }
}
