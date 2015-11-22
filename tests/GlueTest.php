<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue;

use League\Container\Container;
use League\Container\ServiceProvider\ServiceProviderInterface;
use Madewithlove\Glue\Configuration\Configuration;
use Mockery;

class GlueTest extends TestCase
{
    public function testCanCreateWithEmptyConfiguration()
    {
        new Glue(new Configuration());
    }

    public function testCanCreateWithConfiguration()
    {
        $config = [
            'debug' => 'foobar',
            'providers' => [
                'foo',
                'bar',
            ],
        ];

        $glue = new Glue(new Configuration($config));

        $this->assertEquals($config, $glue->getConfiguration()->toArray());
    }

    public function testCanDelegateCallsToRouter()
    {
        $router = Mockery::mock('Router');
        $router->shouldReceive('get')->once()->andReturnUsing(function ($route) {
            return $route;
        });

        $container = new Container();
        $container->add('router', $router);

        $glue = new Glue(new Configuration());
        $glue->setContainer($container);

        $glue->get('foobar');

        $this->assertEquals(['foobar'], $glue->getContainer()->get('routes'));
    }

    public function testDoesntBootTwice()
    {
        $provider = Mockery::mock(ServiceProviderInterface::class);
        $provider->shouldReceive('setContainer')->once();
        $provider->shouldReceive('provides')->once()->andReturn(['foobar']);

        $glue = new Glue(new Configuration([
            'providers' => [$provider],
        ]));

        $glue->boot();
        $glue->boot();
    }

    public function testCanRunConsole()
    {
        $console = Mockery::mock('console');
        $console->shouldReceive('run')->once()->andReturn('foobar');

        $container = new Container();
        $container->add('console', $console);

        $glue = new Glue(new Configuration());
        $glue->setContainer($container);

        $this->assertEquals('foobar', $glue->console());
    }
}
