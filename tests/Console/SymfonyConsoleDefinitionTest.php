<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Console;

use Madewithlove\Glue\Configuration\Configuration;
use Madewithlove\Glue\Console\Commands\TinkerCommand;
use Madewithlove\Glue\Container;
use Madewithlove\Glue\Definitions\Console\SymfonyConsoleDefinition;
use Madewithlove\Glue\Dummies\DummyConsoleCommand;
use Madewithlove\Glue\Glue;
use Madewithlove\Glue\TestCase;
use Symfony\Component\Console\Application;

class SymfonyConsoleDefinitionTest extends TestCase
{
    public function testCanBindCommandsToConsole()
    {
        $glue = new Glue(new Configuration([
            'definitions' => [new SymfonyConsoleDefinition([TinkerCommand::class])],
        ]));

        $glue->boot();

        /** @var Application $console */
        $console = $glue->getContainer()->get('console');
        $this->assertArrayHasKey('tinker', $console->all());
    }

    public function testCanCreateWithDefaultCommands()
    {
        $glue = new Glue();
        $glue->setDefinitionProvider(
            'console',
            SymfonyConsoleDefinition::withDefaultCommands([
                DummyConsoleCommand::class,
            ])
        );

        $glue->boot();

        $console = $glue->getContainer()->get('console');
        $this->assertArrayHasKey('tinker', $console->all());
        $this->assertArrayHasKey('foobar', $console->all());
    }

    public function testCanPassActualCommandInstances()
    {
        $glue = new Glue(new Configuration([
            'definitions' => [new SymfonyConsoleDefinition([
                new TinkerCommand(new Container()),
            ])],
        ]));

        $glue->boot();

        /** @var Application $console */
        $console = $glue->getContainer()->get('console');
        $this->assertArrayHasKey('tinker', $console->all());
    }
}
