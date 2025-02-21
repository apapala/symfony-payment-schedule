<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\PaymentInstruction;
use App\Repository\CurrencyRepository;
use App\Repository\PaymentInstructionRepository;
use App\Repository\ProductTypeRepository;
use Money\Currency;
use Money\Money;

class PaymentScheduleService
{
    public function __construct(
        private readonly PaymentScheduleCalculator $calculator,
        private readonly ProductPaymentRuleResolver $productPaymentRuleResolver,
        private readonly CurrencyRepository $currencyRepository,
        private readonly ProductTypeRepository $productTypeRepository,
        private readonly PaymentInstructionRepository $paymentInstructionRepository,
    ) {
    }

    public function calculateSchedule(
        string $productType,
        int $amount,
        string $currency,
        \DateTimeImmutable $productSoldDate,
    ): array {
        $productType = $this->productTypeRepository->findOneBy(['code' => $productType])
            ?? throw new \InvalidArgumentException('Product type not found');

        $this->currencyRepository->findOneBy(['code' => $currency])
            ?? throw new \InvalidArgumentException('Currency not found');

        if ('UTC' !== $productSoldDate->getTimezone()->getName()) {
            $productSoldDate = (clone $productSoldDate)->setTimezone(new \DateTimeZone('UTC'));
        }

        $instruction = new PaymentInstruction();
        $instruction->setProductType($productType);
        $instruction->setCurrencyCode($currency);
        $instruction->setMoney(new Money(
            $amount,
            new Currency($currency))
        );
        $instruction->setProductSoldDate($productSoldDate);

        $ruleType = $this->productPaymentRuleResolver->resolvePaymentRule(
            $productType,
            $productSoldDate
        );
        $instruction->setRuleType($ruleType);

        $schedules = $this->calculator->calculate($instruction);

        // We can save payment instruction here or pass it through Messenger component for processing later.
        // Data with what schedules will be created will be returned to a calling method.
        $this->paymentInstructionRepository->store($instruction);

        return $schedules;
    }
}
