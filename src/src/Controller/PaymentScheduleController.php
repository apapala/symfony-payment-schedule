<?php

namespace App\Controller;

use App\Request\CalculatePaymentScheduleRequest;
use App\Service\PaymentScheduleService;
use App\Service\ResponseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentScheduleController extends AbstractController
{
    public function __construct(
        private ResponseService $responseService,
        private PaymentScheduleService $paymentScheduleService,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
    ) {
    }

    #[Route('/api/payment-schedule/calculate', name: 'payment_schedule_calculate', methods: ['POST'])]
    public function calculateSchedule(Request $request): JsonResponse
    {
        try {
            $calculateRequest = $this->serializer->deserialize(
                $request->getContent(),
                CalculatePaymentScheduleRequest::class,
                'json'
            );

            $errors = $this->validator->validate($calculateRequest);
            if (count($errors) > 0) {
                /* @phpstan-ignore-next-line */
                return $this->json(['errors' => (string) $errors], 400);
            }

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
            // TODO: Log error properly
        }

        return $this->responseService->error('Something went wrong');
    }
}
