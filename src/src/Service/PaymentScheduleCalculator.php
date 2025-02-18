<?php

namespace App\Service;

use App\Entity\PaymentInstruction;
use App\Entity\PaymentSchedule;
use App\PaymentRule\PaymentRuleFactory;

class PaymentScheduleCalculator
{
    public function __construct(
        private PaymentRuleFactory $ruleFactory,
    ) {
    }

    public function calculate(PaymentInstruction $instruction): array
    {
        $rule = $this->ruleFactory->getRule($instruction->getRuleType());
        $rule->calculatePaymentSchedules($instruction);

        return $instruction->getPaymentSchedules()
            ->map(fn (PaymentSchedule $schedule) => $schedule->toDto())
            ->toArray();
    }
}
