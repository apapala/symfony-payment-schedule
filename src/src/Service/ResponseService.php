<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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

    public function error(?string $message = null, array $errors = [], int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
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
}
