<?php

namespace App\PaymentRule;

use App\Entity\PaymentInstruction;

class JuneRule extends AbstractPaymentRule
{
    public function supports(string $ruleCode): bool
    {
        return $ruleCode === RuleType::JUNE_RULE->value;
    }

    public function calculatePaymentSchedules(PaymentInstruction $instruction): void
    {
        $totalMoney = $instruction->getMoney();
        $soldDate = $instruction->getProductSoldDate();

        $firstInstallment = $totalMoney->multiply('0.3');
        $secondInstallment = $totalMoney->subtract($firstInstallment);

        $this->createPaymentSchedule(
            $instruction,
            $firstInstallment,
            $soldDate
        );

        $endOfMonth = $soldDate->modify('last day of this month 23:59:59');
        $secondPaymentDate = $endOfMonth->modify('+3 months');

        $this->createPaymentSchedule(
            $instruction,
            $secondInstallment,
            $secondPaymentDate
        );
    }
}
