<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Http\Controllers;

use League\Tactician\CommandBus;
use Madewithlove\Glue\Dummies\DummyAbstractController;
use Madewithlove\Glue\Dummies\DummyCommand;
use Madewithlove\Glue\TestCase;
use Mockery;
use Twig_Environment;
use Zend\Diactoros\Response\HtmlResponse;

class AbstractControllerTest extends TestCase
{
    public function testCanRenderView()
    {
        $bus = Mockery::mock(CommandBus::class);
        $twig = Mockery::mock(Twig_Environment::class);
        $twig->shouldReceive('render')->once()->with('index.twig', ['foo' => 'bar'])->andReturn('foobar');

        $controller = new DummyAbstractController($twig, $bus);
        $view = $controller->index();

        $this->assertInstanceOf(HtmlResponse::class, $view);
        $this->assertEquals('foobar', $view->getBody());
    }

    public function testCanDispatchCommands()
    {
        $twig = Mockery::mock(Twig_Environment::class);
        $bus = Mockery::mock(CommandBus::class);
        $bus->shouldReceive('handle')->once()->andReturnUsing(function (DummyCommand $command) {
            return $command->foobar;
        });

        $controller = new DummyAbstractController($twig, $bus);
        $response = $controller->show();

        $this->assertEquals('foobar', $response);
    }
}
