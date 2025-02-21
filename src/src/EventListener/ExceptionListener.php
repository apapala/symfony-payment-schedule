<?php

namespace App\EventListener;

use App\Exception\RequestValidationException;
use App\Service\ExceptionContextGenerator;
use App\Service\ResponseService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionListener
{
    public function __construct(
        private ResponseService $responseService,
        private LoggerInterface $logger,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpException) {
            return;
        }

        if ($exception instanceof RequestValidationException) {
            $event->setResponse(
                $this->responseService->requestValidationError($exception->getViolations())
            );

            return;
        }

        $this->logger->error(
            sprintf('Exception listener error caught: %s', $exception->getMessage()),
            ExceptionContextGenerator::createFromThrowable($exception)
        );

        $event->setResponse(
            $this->responseService->error('Something went wrong.')
        );
    }
}
