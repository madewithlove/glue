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
use Madewithlove\Glue\Glue;
use Madewithlove\Glue\ServiceProviders\Console\PhinxServiceProvider;
use Madewithlove\Glue\TestCase;
use Madewithlove\ServiceProviders\Console\SymfonyConsoleServiceProvider;
use Phinx\Console\Command\Migrate;
use Symfony\Component\Console\Application;

class PhinxServiceProviderTest extends TestCase
{
    public function testCanMirrorPhinxCommands()
    {
        $glue = new Glue(new Configuration([
            'paths' => [
                'migrations' => 'foobar',
            ],
            'providers' => [
                new SymfonyConsoleServiceProvider(),
                new PhinxServiceProvider([
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
