<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class MetricsListener implements EventSubscriberInterface
{
    private LoggerInterface $metricsLogger;

    public function __construct(LoggerInterface $metricsLogger)
    {
        $this->metricsLogger = $metricsLogger;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $event->getRequest()->attributes->set('_start_time', microtime(true));
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$startTime = $request->attributes->get('_start_time')) {
            return;
        }

        $duration = microtime(true) - $startTime;

        $metricsInfo = [
            'duration' => $duration,
            'status' => $event->getResponse()->getStatusCode(),
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
        ];

        if ($route = $request->attributes->get('_route')) {
            $metricsInfo['route'] = $route;
        }

        $this->metricsLogger->info('Request processed', $metricsInfo);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => ['onKernelRequest', 100],
            'kernel.response' => ['onKernelResponse', -100],
        ];
    }
}
