<?php

namespace App\PaymentRule;

use App\Entity\PaymentInstruction;

class DelayMonthRule extends AbstractPaymentRule
{
    public function supports(string $ruleCode): bool
    {
        return $ruleCode === RuleType::DELAY_MONTH_RULE->value;
    }

    public function calculatePaymentSchedules(PaymentInstruction $instruction): void
    {
        $totalMoney = $instruction->getMoney();
        $delayMonth = 1; // TODO: make it configurable in the future

        $dueDate = $instruction->getProductSoldDate()->modify('+'.$delayMonth.' months');

        $this->createPaymentSchedule(
            $instruction,
            $totalMoney,
            $dueDate
        );
    }
}
