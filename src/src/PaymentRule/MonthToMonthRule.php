<?php

namespace App\PaymentRule;

use App\Entity\PaymentInstruction;

class MonthToMonthRule extends AbstractPaymentRule
{
    public function supports(string $ruleCode): bool
    {
        return $ruleCode === RuleType::MONTH_TO_MONTH_RULE->value;
    }

    public function calculatePaymentSchedules(PaymentInstruction $instruction): void
    {
        $totalMoney = $instruction->getMoney();
        $moneyAllocations = $totalMoney->allocateTo(12);
        $date = $instruction->getProductSoldDate();

        foreach ($moneyAllocations as $k => $money) {
            $this->createPaymentSchedule(
                $instruction,
                $money,
                $date->modify('+'.$k.' month')
            );
        }
    }
}
