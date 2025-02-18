<?php

namespace App\Dto;

class PaymentScheduleDto implements \JsonSerializable
{
    public function __construct(
        private float $amount,
        private string $currency,
        private \DateTimeImmutable $dueDate,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'dueDate' => $this->dueDate->format('Y-m-d\TH:i:s.uP'),
        ];
    }
}
