<?php

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class RequestValidationException extends \RuntimeException
{
    public function __construct(
        private readonly ConstraintViolationListInterface $violations,
        string $message = 'Request validation failed',
    ) {
        parent::__construct($message);
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
