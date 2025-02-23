<?php

namespace App\Message;

class ProcessPaymentSchedulesMessage
{
    public function __construct(
        private readonly int $paymentInstructionId,
    ) {
    }

    public function getPaymentInstructionId(): int
    {
        return $this->paymentInstructionId;
    }
}
