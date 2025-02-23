<?php

namespace App\Controller;

use App\Request\CalculatePaymentScheduleRequest;
use App\Request\RequestValidator;
use App\Service\ExceptionContextGenerator;
use App\Service\PaymentScheduleService;
use App\Service\ResponseService;
use OpenApi\Attributes as OA;
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

    #[OA\Post(
        path: '/api/payment-schedule/calculate',
        description: 'Calculates a payment schedule based on product details',
        summary: 'Calculate payment schedule',
        security: [['ApiKeyAuth' => []]],
        tags: ['Payment Schedule']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'productName', type: 'string', example: 'Premium Subscription'),
                new OA\Property(property: 'productType', type: 'string', example: 'premium_sub'),
                new OA\Property(
                    property: 'productPrice',
                    properties: [
                        new OA\Property(property: 'amount', type: 'number', format: 'int', example: 500000),
                        new OA\Property(property: 'currency', type: 'string', example: 'PLN'),
                    ],
                    type: 'object'
                ),
                new OA\Property(property: 'productSoldDate', type: 'string', format: 'date-time', example: '2024-02-23T12:00:00Z'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Payment schedules scheduled',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Request validation error',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Request validation failed'),
                new OA\Property(
                    property: 'errors',
                    properties: [
                        new OA\Property(
                            property: 'productPrice[currency]',
                            type: 'string',
                            example: 'This field is missing.'
                        ),
                    ],
                    type: 'object'
                ),
            ]
        )
    )]
    #[Route('/api/payment-schedule/calculate', methods: ['POST'])]
    public function calculateSchedule(Request $request): JsonResponse
    {
        try {
            /** @var CalculatePaymentScheduleRequest $calculateRequest */
            $calculateRequest = $this->requestValidator->validateRequest(
                $request->getContent(),
                CalculatePaymentScheduleRequest::class
            );

            $this->paymentScheduleService->calculateSchedule(
                $calculateRequest->getProductType(),
                $calculateRequest->getProductPriceAmount(),
                $calculateRequest->getProductPriceCurrency(),
                $calculateRequest->getProductSoldDate()
            );

            return $this->responseService->success();
        } catch (\Throwable $e) {
            $this->logger->error('Error generating payment schedule.', ExceptionContextGenerator::createFromThrowable($e));
            throw $e; // Let ExceptionListener handle the response for now
        }
    }
}
