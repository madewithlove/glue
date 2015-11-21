<?php

namespace Madewithlove\Glue\Services;

use InvalidArgumentException;
use League\Route\RouteCollection;
use PHPUnit_Framework_TestCase;

class UrlGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var UrlGenerator
     */
    protected $generator;

    public function setUp()
    {
        $routes = new RouteCollection();
        $urls   = [
            $routes->get('users', 'History\Http\Controllers\FooController::index'),
            $routes->get('users/{user}', 'History\Http\Controllers\FooController::show'),
        ];

        $this->generator = new UrlGenerator('History', $urls);
    }

    public function testCanGeneratorUrlToRoute()
    {
        $this->assertEquals('/users', $this->generator->to('foo.index'));
        $this->assertEquals('/users/foobar', $this->generator->to('foo.show', ['user' => 'foobar']));
        $this->assertEquals('/users/foobar', $this->generator->to('foo.show', 'foobar'));
    }

    public function testThrowsExceptionOnInvalidRoute()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->generator->to('foo.sdfsdf');
    }
}
