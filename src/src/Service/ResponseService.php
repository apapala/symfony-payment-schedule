<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ResponseService
{
    public function success(?string $message = null, array $data = [], int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'status' => 'success',
        ];

        if (null !== $message) {
            $response['message'] = $message;
        }

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return new JsonResponse($response, $statusCode);
    }

    public function error(?string $message = null, array $errors = [], int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        $response = [
            'status' => 'error',
        ];

        if (null !== $message) {
            $response['message'] = $message;
        }

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return new JsonResponse($response, $statusCode);
    }

    public function requestValidationError(ConstraintViolationListInterface $violations): JsonResponse
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return new JsonResponse([
            'status' => 'error',
            'message' => 'Request validation failed',
            'errors' => $errors,
        ], Response::HTTP_BAD_REQUEST);
    }
}
