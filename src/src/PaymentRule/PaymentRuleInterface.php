<?php

namespace App\PaymentRule;

use App\Entity\PaymentInstruction;

interface PaymentRuleInterface
{
    public function calculatePaymentSchedules(PaymentInstruction $instruction): void;

    public function supports(string $ruleCode): bool;
}
