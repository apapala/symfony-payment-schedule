<?php

namespace App\PaymentRule;

use App\Entity\PaymentInstruction;
use App\Entity\PaymentSchedule;
use Money\Money;

abstract class AbstractPaymentRule implements PaymentRuleInterface
{
    protected function createPaymentSchedule(
        PaymentInstruction $instruction,
        Money $money,
        \DateTimeImmutable $dueDate,
    ): PaymentSchedule {
        $schedule = new PaymentSchedule();
        $schedule->setMoney($money);
        $schedule->setDueDate($dueDate);
        $instruction->addPaymentSchedule($schedule);

        return $schedule;
    }
}
