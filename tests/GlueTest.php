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
use Interop\Container\ServiceProviderInterface;
use League\Container\Container;
use League\Tactician\CommandBus;
use Madewithlove\Glue\Configuration\Configuration;
use Madewithlove\Glue\Dummies\Definitions\MockRouterServiceProvider;
use Madewithlove\Glue\Dummies\DummyController;
use Madewithlove\Glue\Http\Middlewares\LeagueRouteMiddleware;
use Madewithlove\ServiceProviders\Bridges\LeagueContainerDecorator;
use Madewithlove\ServiceProviders\Http\LeagueRouteServiceProvider;
use Madewithlove\ServiceProviders\Http\RelayServiceProvider;
use Madewithlove\ServiceProviders\Http\ZendDiactorosServiceProvider;
use Madewithlove\ServiceProviders\Utilities\Parameter;
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
        $glue->setServiceProviders([new MockRouterServiceProvider()]);

        $glue->get('foobar');

        $this->assertEquals(['foobar'], $glue->getContainer()->get('routes'));
    }

    public function testDoesntBootTwice()
    {
        $provider = Mockery::mock(ServiceProviderInterface::class);
        $provider->shouldReceive('getServices')->once()->andReturn(['foobar' => new Parameter('foobar')]);

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

        $container = new LeagueContainerDecorator(new Container());
        $container->add('console', $console);

        $glue = new Glue(new Configuration());
        $glue->setContainer($container);

        $this->assertEquals('foobar', $glue->console());
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
            'providers' => [
                new LeagueRouteServiceProvider(),
                new RelayServiceProvider([LeagueRouteMiddleware::class]),
            ],
        ]), $container);

        $glue->get('foobar', DummyController::class.'::index');
        $response = $glue->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testCanDeclareConfigurationFluently()
    {
        $glue = new Glue(new Configuration());
        $glue
            ->setPaths(['cache' => 'storage/cache'])
            ->setServiceProviders([new LeagueRouteServiceProvider()])
            ->setMiddlewares([LeagueRouteMiddleware::class]);

        $this->assertEquals(['cache' => 'storage/cache'], $glue->getPaths());
        $this->assertEquals([LeagueRouteMiddleware::class], $glue->getMiddlewares());
        $this->assertEquals([new LeagueRouteServiceProvider()], $glue->getServiceProviders());
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
        $app->setServiceProviders([
            new ZendDiactorosServiceProvider(),
            new MockRouterServiceProvider(),
        ]);

        $app->get('foo', 'bar');

        $app->run();
    }

    public function testCanLoadDotenvFiles()
    {
        $path = realpath(__DIR__.'/..').'/.env';
        file_put_contents($path, 'FOO=bar');

        new Glue();

        unlink($path);
        $this->assertEquals('bar', $_ENV['FOO']);
    }
}
