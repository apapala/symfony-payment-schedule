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
        path: '/api/payment-instructions',
        description: 'Creates a new payment instruction with calculated schedule',
        summary: 'Create payment instruction',
        security: [['ApiKeyAuth' => []]],
        tags: ['Payment Instructions']
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
        response: 201,
        description: 'Payment instruction created',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'data', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                ], type: 'object'),
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
    #[Route('/api/payment-instructions', methods: ['POST'])]
    public function createPaymentInstruction(Request $request): JsonResponse
    {
        try {
            /** @var CalculatePaymentScheduleRequest $calculateRequest */
            $calculateRequest = $this->requestValidator->validateRequest(
                $request->getContent(),
                CalculatePaymentScheduleRequest::class
            );

            $instructionId = $this->paymentScheduleService->calculateSchedule(
                $calculateRequest->getProductType(),
                $calculateRequest->getProductPriceAmount(),
                $calculateRequest->getProductPriceCurrency(),
                $calculateRequest->getProductSoldDate()
            );

            return $this->responseService->resourceCreated(data: ['id' => $instructionId]);
        } catch (\Throwable $e) {
            $this->logger->error('Error creating payment instruction.', ExceptionContextGenerator::createFromThrowable($e));
            throw $e;
        }
    }

    #[OA\Get(
        path: '/api/payment-instructions/{id}',
        description: 'Retrieves payment instruction details including payment schedules',
        summary: 'Get payment instruction details',
        security: [['ApiKeyAuth' => []]],
        tags: ['Payment Instructions']
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Payment instruction ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Payment instruction details',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(
                            property: 'productType',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Premium Sub'),
                                    new OA\Property(property: 'code', type: 'string', example: 'premium_sub'),
                                    new OA\Property(property: 'default_payment_rule', type: 'string', example: 'month_to_month_rule'),
                                ]
                            )
                        ),
                        new OA\Property(property: 'productSoldDate', type: 'string', format: 'date-time'),
                        new OA\Property(
                            property: 'paymentSchedules',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(
                                        property: 'money',
                                        type: 'array',
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: 'amount', type: 'number', example: 100000),
                                                new OA\Property(property: 'currency', type: 'string', example: 'PLN'),
                                            ]
                                        )
                                    ),
                                    new OA\Property(property: 'dueDate', type: 'string', format: 'date-time'),
                                ]
                            )
                        ),
                        new OA\Property(
                            property: 'money',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'amount', type: 'number', example: 100000),
                                    new OA\Property(property: 'currency', type: 'string', example: 'PLN'),
                                ]
                            )
                        ),
                    ],
                    type: 'object'
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Payment instruction not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Payment instruction not found'),
            ]
        )
    )]
    #[Route('/api/payment-instructions/{id}', methods: ['GET'])]
    public function getPaymentInstruction(int $id): JsonResponse
    {
        try {
            $paymentInstruction = $this->paymentScheduleService->getPaymentInstruction($id);

            return $this->json($paymentInstruction);
        } catch (\Throwable $e) {
            $this->logger->error('Error retrieving payment instruction.', ExceptionContextGenerator::createFromThrowable($e));
            throw $e;
        }
    }
}
