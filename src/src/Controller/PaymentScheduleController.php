<?php

namespace App\Controller;

use App\Request\CalculatePaymentScheduleRequest;
use App\Request\RequestValidator;
use App\Service\ExceptionContextGenerator;
use App\Service\PaymentScheduleService;
use App\Service\ResponseService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentScheduleController extends AbstractController
{
    public function __construct(
        private readonly ResponseService $responseService,
        private readonly PaymentScheduleService $paymentScheduleService,
        private readonly RequestValidator $requestValidator,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('/api/payment-schedule/calculate', methods: ['POST'])]
    public function calculateSchedule(Request $request): JsonResponse
    {
        try {
            /** @var CalculatePaymentScheduleRequest $calculateRequest */
            $calculateRequest = $this->requestValidator->validateRequest(
                $request->getContent(),
                CalculatePaymentScheduleRequest::class
            );

            $schedules = $this->paymentScheduleService->calculateSchedule(
                $calculateRequest->getProductType(),
                $calculateRequest->getProductPriceAmount(),
                $calculateRequest->getProductPriceCurrency(),
                $calculateRequest->getProductSoldDate()
            );

            return $this->responseService->success(
                data: $schedules
            );
        } catch (\Throwable $e) {
            $this->logger->error('Error generating payment schedule.', ExceptionContextGenerator::createFromThrowable($e));
            throw $e; // Let ExceptionListener handle the response for now
        }
    }
}
