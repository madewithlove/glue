<?php
namespace Madewithlove\Glue\CommandBus;

use League\Container\Container;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use Madewithlove\Glue\Dummies\DummyCommand;
use Madewithlove\Glue\Dummies\DummyHandler;
use Madewithlove\Glue\TestCase;
use Mockery;

class ContainerLocatorTest extends TestCase
{
    public function testCanProperlyLocateHandler()
    {
        $handler = Mockery::mock();
        $handler->shouldReceive('handle')->once();

        $container = new Container();
        $container->add(DummyHandler::class, $handler);

        $bus = new CommandBus([
            new CommandHandlerMiddleware(
                new ClassNameExtractor(),
                new ContainerLocator($container),
                new HandleInflector()
            ),
        ]);

        $bus->handle(new DummyCommand());
    }
}
