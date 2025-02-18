<?php

namespace App\EventListener;

use App\Service\ExceptionContextGenerator;
use App\Service\ResponseService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

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

        $this->logger->error(
            sprintf('Exception listener error caught: %s', $exception->getMessage()),
            ExceptionContextGenerator::createFromThrowable($exception)
        );

        $event->setResponse($this->responseService->error('Something went wrong.'));
    }
}
