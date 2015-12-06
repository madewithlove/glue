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
use Madewithlove\Glue\Definitions\Console\PhinxDefinition;
use Madewithlove\Glue\Definitions\Console\SymfonyConsoleDefinition;
use Madewithlove\Glue\Glue;
use Madewithlove\Glue\TestCase;
use Phinx\Console\Command\Migrate;
use Symfony\Component\Console\Application;

class PhinxDefinitionTest extends TestCase
{
    public function testCanMirrorPhinxCommands()
    {
        $glue = new Glue(new Configuration([
            'paths' => [
              'migrations' => 'foobar',
            ],
            'definitions' => [
              new SymfonyConsoleDefinition(),
                new PhinxDefinition([
                    'paths' => ['migrations' => 'foobar'],
                ]),
            ],
        ]));

        $glue->boot();
        $console = $glue->getContainer()->get('console');

        /* @var Application $console */
        $this->assertArrayHasKey('migrate:migrate', $console->all());

        /** @var Migrate $command */
        $command = $console->get('migrate:migrate');
        $config = $command->getConfig();
        $this->assertEquals('foobar', $config['paths']['migrations']);
    }
}
