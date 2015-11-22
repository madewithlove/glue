<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Console;

use Madewithlove\Glue\Configuration\Configuration;
use Madewithlove\Glue\Console\Commands\TinkerCommand;
use Madewithlove\Glue\Glue;
use Madewithlove\Glue\TestCase;
use Symfony\Component\Console\Application;

class ConsoleServiceProviderTest extends TestCase
{
    public function testCanBindCommandsToConsole()
    {
        $glue = new Glue(new Configuration([
            'providers' => [ConsoleServiceProvider::class],
            'commands' => [TinkerCommand::class],
        ]));

        $glue->boot();

        /** @var Application $console */
        $console = $glue->getContainer()->get('console');
        $this->assertArrayHasKey('tinker', $console->all());
    }
}
