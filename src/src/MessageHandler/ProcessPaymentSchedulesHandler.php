<?php

namespace App\MessageHandler;

use App\Message\ProcessPaymentSchedulesMessage;
use App\Service\PaymentScheduleService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProcessPaymentSchedulesHandler
{
    public function __construct(
        private readonly PaymentScheduleService $paymentScheduleService,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(ProcessPaymentSchedulesMessage $message): void
    {
        try {
            $this->paymentScheduleService->handleMessage($message);

            $this->logger->info('Payment schedules processed successfully', [
                'payment_instruction_id' => $message->getPaymentInstructionId(),
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Error processing payment schedules', [
                'payment_instruction_id' => $message->getPaymentInstructionId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
