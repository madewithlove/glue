<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue;

use Illuminate\Container\Container as IlluminateContainer;
use League\Container\Container;
use League\Container\ServiceProvider\ServiceProviderInterface;
use League\Tactician\CommandBus;
use Madewithlove\Glue\Configuration\Configuration;
use Madewithlove\Glue\Dummies\DummyController;
use Madewithlove\Glue\Dummies\Providers\FirstProvider;
use Madewithlove\Glue\Dummies\Providers\MockRouterProvider;
use Madewithlove\Glue\Dummies\Providers\SecondProvider;
use Madewithlove\Glue\Dummies\Providers\ThirdProvider;
use Madewithlove\Glue\Http\Middlewares\LeagueRouteMiddleware;
use Madewithlove\Glue\Http\Providers\LeagueRouteServiceProvider;
use Madewithlove\Glue\Http\Providers\RelayServiceProvider;
use Madewithlove\Glue\Http\Providers\RequestServiceProvider;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\Uri;

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
        $glue = new Glue(new Configuration());
        $glue->setProviders([MockRouterProvider::class]);

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

    public function testDoesntRunIfNoRoutes()
    {
        $emitter = Mockery::mock(SapiEmitter::class);
        $emitter->shouldNotReceive('emit');

        $container = new Container();
        $container->add(SapiEmitter::class, $emitter);

        $glue = new Glue(new Configuration(), $container);
        $glue->run();
    }

    public function testCanRunApp()
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getMethod')->andReturn('GET');
        $request->shouldReceive('getUri')->andReturn(new Uri('/foobar'));

        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getBody->isWritable')->andReturn(true);
        $response->shouldReceive('getBody->write')->with('foobar');

        $emitter = Mockery::mock(SapiEmitter::class);
        $emitter->shouldReceive('emit')->once()->with($response);

        $bus = Mockery::mock(CommandBus::class);
        $bus->shouldReceive('handle')->andReturn('foobar');
        $bus->shouldIgnoreMissing();

        $container = new Container();
        $container->add(ServerRequestInterface::class, $request);
        $container->add(ResponseInterface::class, $response);
        $container->add(DummyController::class, new DummyController($bus));
        $container->add(SapiEmitter::class, $emitter);

        $glue = new Glue(new Configuration([
            'debug' => false,
            'providers' => [LeagueRouteServiceProvider::class, RelayServiceProvider::class],
            'middlewares' => [LeagueRouteMiddleware::class],
        ]), $container);

        $glue->get('foobar', DummyController::class.'::index');
        $glue->run();
    }

    public function testCanDeclareConfigurationFluently()
    {
        $glue = new Glue(new Configuration());
        $glue
            ->setPaths(['cache' => 'storage/cache'])
            ->setProviders([LeagueRouteServiceProvider::class])
            ->setMiddlewares([LeagueRouteMiddleware::class]);

        $this->assertEquals(['cache' => 'storage/cache'], $glue->getPaths());
        $this->assertEquals([LeagueRouteMiddleware::class], $glue->getMiddlewares());
        $this->assertEquals([LeagueRouteServiceProvider::class], $glue->getProviders());
    }

    public function testCanUserOtherContainers()
    {
        $container = new IlluminateContainer();
        $container->singleton('foobar', function () {
            return 'foobar';
        });

        $glue = new Glue(new Configuration(), $container);

        $this->assertEquals('foobar', $glue->getContainer()->get('foobar'));
    }

    public function testOrderOfProvidersDoesNotMatter()
    {
        $this->expectOutputString('12');

        $app = new Glue();
        $app->setProviders([
            FirstProvider::class,
            ThirdProvider::class,
            SecondProvider::class,
        ]);

        $app->boot();
    }

    public function testCanUseDifferentMiddlewaresPipeline()
    {
        $container = new Container();
        $container->add(SapiEmitter::class, function () {
            $emitter = Mockery::mock(SapiEmitter::class);
            $emitter->shouldReceive('emit')->once()->andReturnUsing(function (Response $response) {
                $this->assertEquals(302, $response->getStatusCode());
            });

            return $emitter;
        });

        $container->add('pipeline', function () {
            return function ($request, Response $response) {
                return $response->withStatus(302);
            };
        });

        $app = new Glue();
        $app->setContainer($container);
        $app->setProviders([
            RequestServiceProvider::class,
            MockRouterProvider::class,
        ]);

        $app->get('foo', 'bar');

        $app->run();
    }
}
