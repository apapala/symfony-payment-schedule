<?php

namespace App\Entity;

use App\Repository\PaymentInstructionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Money\Currency;
use Money\Money;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: PaymentInstructionRepository::class)]
class PaymentInstruction
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['default'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ProductType::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['default'])]
    private ?ProductType $productType = null;

    #[ORM\Column]
    private string $amount;

    #[ORM\Column(length: 3)]
    #[Ignore]
    private string $currencyCode;

    #[ORM\Column(length: 50)]
    #[Groups(['default'])]
    private string $ruleType;

    #[ORM\OneToMany(targetEntity: PaymentSchedule::class, mappedBy: 'paymentInstruction', cascade: ['persist'])]
    #[Groups(['default'])]
    private Collection $paymentSchedules;

    #[ORM\Column]
    #[Groups(['default'])]
    private ?\DateTimeImmutable $productSoldDate = null;

    #[Groups(['default'])]
    #[SerializedName('money')]
    private ?Money $money = null;

    public function __construct()
    {
        $this->paymentSchedules = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductType(): ?ProductType
    {
        return $this->productType;
    }

    public function setProductType(?ProductType $productType): self
    {
        $this->productType = $productType;

        return $this;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): self
    {
        $this->currencyCode = strtoupper($currencyCode);

        return $this;
    }

    public function getRuleType(): string
    {
        return $this->ruleType;
    }

    public function setRuleType(string $ruleType): self
    {
        $this->ruleType = $ruleType;

        return $this;
    }

    public function getPaymentSchedules(): Collection
    {
        return $this->paymentSchedules;
    }

    public function addPaymentSchedule(PaymentSchedule $paymentSchedule): self
    {
        if (!$this->paymentSchedules->contains($paymentSchedule)) {
            $this->paymentSchedules[] = $paymentSchedule;
            $paymentSchedule->setPaymentInstruction($this);
        }

        return $this;
    }

    public function getProductSoldDate(): ?\DateTimeImmutable
    {
        return $this->productSoldDate;
    }

    public function setProductSoldDate(\DateTimeImmutable $date): self
    {
        $this->productSoldDate = $date;

        return $this;
    }

    public function getMoney(): Money
    {
        if (null === $this->money) {
            $this->money = new Money($this->amount, new Currency($this->currencyCode));
        }

        return $this->money;
    }

    public function setMoney(Money $money): self
    {
        $this->money = $money;
        $this->amount = $money->getAmount();

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
        ];
    }
}
