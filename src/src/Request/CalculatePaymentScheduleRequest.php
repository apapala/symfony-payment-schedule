<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CalculatePaymentScheduleRequest
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private string $productType;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    private string $productName;

    #[Assert\Collection(
        fields: [
            'amount' => [
                new Assert\NotNull(),
                new Assert\Type('integer'),
            ],
            'currency' => [
                new Assert\NotNull(),
                new Assert\Length(exactly: 3),
            ],
        ],
        allowExtraFields: false
    )]
    private array $productPrice;

    #[Assert\NotNull]
    private \DateTimeImmutable $productSoldDate;

    public function getProductType(): string
    {
        return $this->productType;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getProductPrice(): array
    {
        return $this->productPrice;
    }

    public function getProductPriceAmount(): int
    {
        return $this->getProductPrice()['amount'];
    }

    public function getProductPriceCurrency(): string
    {
        return $this->getProductPrice()['currency'];
    }

    public function getProductSoldDate(): \DateTimeImmutable
    {
        return $this->productSoldDate;
    }

    public function setProductSoldDate(\DateTimeImmutable|string $productSoldDate): self
    {
        if (is_string($productSoldDate)) {
            try {
                $this->productSoldDate = new \DateTimeImmutable($productSoldDate);
            } catch (\Throwable $e) {
                $this->productSoldDate = new \DateTimeImmutable('now');
            }
        } else {
            $this->productSoldDate = $productSoldDate;
        }

        return $this;
    }

    public function setProductType(string $productType): void
    {
        $this->productType = $productType;
    }

    public function setProductName(string $productName): void
    {
        $this->productName = $productName;
    }

    public function setProductPrice(array $productPrice): void
    {
        $this->productPrice = $productPrice;
    }
}
