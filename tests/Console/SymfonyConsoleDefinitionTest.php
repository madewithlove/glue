<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Console;

use League\Container\Container;
use Madewithlove\Glue\Configuration\Configuration;
use Madewithlove\Glue\Console\Commands\TinkerCommand;
use Madewithlove\Glue\Dummies\DummyConsoleCommand;
use Madewithlove\Glue\Glue;
use Madewithlove\Glue\ServiceProviders\Console\SymfonyConsoleDefinition;
use Madewithlove\Glue\TestCase;
use Symfony\Component\Console\Application;

class SymfonyConsoleDefinitionTest extends TestCase
{
    public function testCanBindCommandsToConsole()
    {
        $glue = new Glue(new Configuration([
            'providers' => [new SymfonyConsoleDefinition([TinkerCommand::class])],
        ]));

        $glue->boot();

        /** @var Application $console */
        $console = $glue->getContainer()->get('console');
        $this->assertArrayHasKey('tinker', $console->all());
    }

    public function testCanCreateWithDefaultCommands()
    {
        $glue = new Glue();
        $glue->setServiceProvider(
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
            'providers' => [new SymfonyConsoleDefinition([
                new TinkerCommand(new Container()),
            ])],
        ]));

        $glue->boot();

        /** @var Application $console */
        $console = $glue->getContainer()->get('console');
        $this->assertArrayHasKey('tinker', $console->all());
    }
}
