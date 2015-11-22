<?php
namespace Madewithlove\Glue\Traits;

use League\Tactician\CommandBus;
use Madewithlove\Glue\Dummies\DummyCommand;
use Madewithlove\Glue\Dummies\DummyController;
use Madewithlove\Glue\TestCase;
use Mockery;
use Psr\Http\Message\ServerRequestInterface;

class DispatchesCommandsTest extends TestCase
{
    public function testCanDispatchCommand()
    {
        $bus = Mockery::mock(CommandBus::class);
        $bus->shouldReceive('handle')->times(3)->andReturnUsing(function($command) {
            return $command->foobar;
        });

        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getAttributes')->andReturn(['foobar' => 'foobar']);

        $controller = new DummyController($bus);
        $this->assertEquals('foobar', $controller->index());
        $this->assertEquals('foobar', $controller->create());
        $this->assertEquals('foobar', $controller->show($request));
    }
}
