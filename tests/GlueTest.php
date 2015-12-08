<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue;

use Assembly\ParameterDefinition;
use Illuminate\Container\Container as IlluminateContainer;
use Interop\Container\Definition\DefinitionProviderInterface;
use League\Tactician\CommandBus;
use Madewithlove\Definitions\Http\LeagueRouteDefinition;
use Madewithlove\Definitions\Http\RelayDefinition;
use Madewithlove\Definitions\Http\ZendDiactorosDefinition;
use Madewithlove\Glue\Configuration\Configuration;
use Madewithlove\Glue\Dummies\Definitions\MockRouterDefinition;
use Madewithlove\Glue\Dummies\DummyController;
use Madewithlove\Glue\Http\Middlewares\LeagueRouteMiddleware;
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
        $glue->setDefinitionProviders([new MockRouterDefinition()]);

        $glue->get('foobar');

        $this->assertEquals(['foobar'], $glue->getContainer()->get('routes'));
    }

    public function testDoesntBootTwice()
    {
        $provider = Mockery::mock(DefinitionProviderInterface::class);
        $provider->shouldReceive('getDefinitions')->once()->andReturn([new ParameterDefinition('foobar', 'foobar')]);

        $glue = new Glue(new Configuration([
            'definitions' => [$provider],
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
            'definitions' => [new LeagueRouteDefinition(), new RelayDefinition([LeagueRouteMiddleware::class])],
        ]), $container);

        $glue->get('foobar', DummyController::class.'::index');
        $glue->run();
    }

    public function testCanDeclareConfigurationFluently()
    {
        $glue = new Glue(new Configuration());
        $glue
            ->setPaths(['cache' => 'storage/cache'])
            ->setDefinitionProviders([new LeagueRouteDefinition()])
            ->setMiddlewares([LeagueRouteMiddleware::class]);

        $this->assertEquals(['cache' => 'storage/cache'], $glue->getPaths());
        $this->assertEquals([LeagueRouteMiddleware::class], $glue->getMiddlewares());
        $this->assertEquals([new LeagueRouteDefinition()], $glue->getDefinitionProviders());
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
        $app->setDefinitionProviders([
            new ZendDiactorosDefinition(),
            new MockRouterDefinition(),
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
