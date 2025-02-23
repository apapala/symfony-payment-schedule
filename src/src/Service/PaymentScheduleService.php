<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\PaymentInstruction;
use App\Message\ProcessPaymentSchedulesMessage;
use App\PaymentRule\PaymentRuleFactory;
use App\Repository\CurrencyRepository;
use App\Repository\PaymentInstructionRepository;
use App\Repository\ProductTypeRepository;
use Money\Currency;
use Money\Money;
use Symfony\Component\Messenger\MessageBusInterface;

class PaymentScheduleService
{
    public function __construct(
        private readonly ProductPaymentRuleResolver $productPaymentRuleResolver,
        private readonly CurrencyRepository $currencyRepository,
        private readonly ProductTypeRepository $productTypeRepository,
        private readonly PaymentInstructionRepository $paymentInstructionRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly PaymentRuleFactory $ruleFactory,
    ) {
    }

    public function calculateSchedule(
        string $productType,
        int $amount,
        string $currency,
        \DateTimeImmutable $productSoldDate,
    ): void {
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

        $this->paymentInstructionRepository->store($instruction);

        $this->messageBus->dispatch(new ProcessPaymentSchedulesMessage($instruction->getId()));
    }

    public function handleMessage(ProcessPaymentSchedulesMessage $message): void
    {
        $instruction = $this->paymentInstructionRepository->find($message->getPaymentInstructionId())
            ?? throw new \InvalidArgumentException('Payment instruction not found');

        $rule = $this->ruleFactory->getRule($instruction->getRuleType());
        $rule->calculatePaymentSchedules($instruction);

        $this->paymentInstructionRepository->store($instruction);
    }
}
