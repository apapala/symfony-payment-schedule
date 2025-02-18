<?php

namespace App\PaymentRule;

use App\Entity\PaymentInstruction;

class OnePaymentRule extends AbstractPaymentRule
{
    public function supports(string $ruleCode): bool
    {
        return $ruleCode === RuleType::ONE_PAYMENT_RULE->value;
    }

    public function calculatePaymentSchedules(PaymentInstruction $instruction): void
    {
        $this->createPaymentSchedule(
            $instruction,
            $instruction->getMoney(),
            new \DateTimeImmutable()
        );
    }
}
