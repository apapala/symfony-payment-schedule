<?php

namespace App\Controller;

use App\Command\TestCommand;
use App\Service\ExceptionContextGenerator;
use App\Service\ResponseService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HttpToCommandController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    #[Route('/private/test-command', methods: 'GET')]
    public function testCommand(TestCommand $testCommand, ResponseService $responseService): JsonResponse
    {
        try {
            $input = new ArrayInput([]);
            $output = new NullOutput();

            if (Command::SUCCESS === $testCommand->run($input, $output)) {
                return $responseService->success();
            }
        } catch (\Throwable $e) {
            $this->logger->error('Test command failed.', ExceptionContextGenerator::createFromThrowable($e));
        }

        return $responseService->error();
    }
}
