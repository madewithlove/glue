<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue;

use Madewithlove\Glue\Dummies\Definitions\DummyDefinition;

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
}
