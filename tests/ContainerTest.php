<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue;

use Assembly\ArrayDefinitionProvider;
use Assembly\ParameterDefinition;
use Madewithlove\Glue\Definitions\DefinitionTypes\ExtendDefinition;
use Madewithlove\Glue\Dummies\Definitions\DummyDefinition;
use Mockery;

class ContainerTest extends TestCase
{
    public function testDoesntResolveDefinitionTwice()
    {
        $container = new Container();
        $container->addDefinitionProvider(new DummyDefinition());

        $container->get('foo');
        $container->add('foo', 'baz');
        $container->get('foo');

        $this->assertEquals('baz', $container->get('foo'));
    }

    public function testCanAddExtensionsToDefinition()
    {
        $service = Mockery::mock('foobar');
        $service->shouldReceive('someMethod')->once()->with('foobar');

        $container = new Container();

        $container->addDefinitionProvider(new ArrayDefinitionProvider([
            'foobar' => (new ParameterDefinition($service)),
            'extenstion' => (new ExtendDefinition('foobar'))->addMethodCall('someMethod', 'foobar'),
        ]));

        $container->get('foobar');
    }
}
