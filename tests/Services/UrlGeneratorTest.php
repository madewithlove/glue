<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Services;

use InvalidArgumentException;
use League\Route\RouteCollection;
use PHPUnit\Framework\TestCase;

class UrlGeneratorTest extends TestCase
{
    /**
     * @var UrlGenerator
     */
    protected $generator;

    public function setUp()
    {
        $routes = new RouteCollection();
        $urls = [
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
        $this->expectException(InvalidArgumentException::class);
        $this->generator->to('foo.sdfsdf');
    }
}
