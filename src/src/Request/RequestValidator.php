<?php

namespace App\Request;

use App\Exception\RequestValidationException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestValidator
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $type
     */
    public function validateRequest(string $content, string $type, string $format = 'json'): object
    {
        $request = $this->serializer->deserialize($content, $type, $format);

        $violations = $this->validator->validate($request);
        if (count($violations) > 0) {
            throw new RequestValidationException($violations);
        }

        return $request;
    }
}
