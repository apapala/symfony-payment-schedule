<?php

namespace App\Entity;

use App\PaymentRule\RuleType;
use App\Repository\ProductTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductTypeRepository::class)]
class ProductType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 50)]
    private string $defaultPaymentRule;

    public function __construct()
    {
        $this->defaultPaymentRule = RuleType::ONE_PAYMENT_RULE->value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDefaultPaymentRule(): string
    {
        return $this->defaultPaymentRule;
    }

    public function setDefaultPaymentRule(string $ruleType): self
    {
        $this->defaultPaymentRule = $ruleType;

        return $this;
    }
}
