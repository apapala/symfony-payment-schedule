<?php

namespace App\Tests\Command;

use App\Command\TestCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommandTest extends TestCase
{
    public function testReturnsSuccess(): void
    {
        $sut = new TestCommand();

        $result = $sut->run(
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class),
        );

        $this->assertSame(Command::SUCCESS, $result);
    }
}
