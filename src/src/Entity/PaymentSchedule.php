<?php

namespace App\Entity;

use App\Dto\PaymentScheduleDto;
use Doctrine\ORM\Mapping as ORM;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

#[ORM\Entity]
class PaymentSchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: PaymentInstruction::class, inversedBy: 'paymentSchedules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PaymentInstruction $paymentInstruction = null;

    #[ORM\Column]
    private string $amount;

    private ?Money $money = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dueDate = null;

    #[ORM\Column]
    private bool $isPaid = false;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $paidAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaymentInstruction(): ?PaymentInstruction
    {
        return $this->paymentInstruction;
    }

    public function setPaymentInstruction(?PaymentInstruction $paymentInstruction): self
    {
        $this->paymentInstruction = $paymentInstruction;

        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDueDate(): ?\DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeImmutable $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function isPaid(): bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(?\DateTimeImmutable $paidAt): self
    {
        $this->paidAt = $paidAt;

        return $this;
    }

    public function getMoney(): Money
    {
        if (null === $this->money) {
            $this->money = new Money(
                $this->amount,
                $this->paymentInstruction->getMoney()->getCurrency()
            );
        }

        return $this->money;
    }

    public function setMoney(Money $money): self
    {
        $this->money = $money;
        $this->amount = $money->getAmount();

        return $this;
    }

    public function toDto(): PaymentScheduleDto
    {
        $currencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($currencies);

        return new PaymentScheduleDto(
            (float) $moneyFormatter->format($this->getMoney()),
            $this->paymentInstruction->getMoney()->getCurrency()->getCode(),
            $this->dueDate
        );
    }
}
