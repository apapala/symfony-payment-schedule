<?php

namespace App\Service;

class ExceptionContextGenerator
{
    public static function createFromThrowable(\Throwable $e): array
    {
        return [
            'message' => $e->getMessage(),
            'exception_class' => $e::class,
            'stack_trace' => $e->getTrace(),
        ];
    }
}
