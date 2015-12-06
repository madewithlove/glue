<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Configuration;

use Madewithlove\Glue\Dummies\Definitions\DummyDefinition;
use Madewithlove\Glue\TestCase;

class AbstractConfigurationTest extends TestCase
{
    public function testCanOverrideParticularProvider()
    {
        $configuration = new DefaultConfiguration();
        $configuration->setDefinitionProvider('console', new DummyDefinition());

        $this->assertInstanceOf(DummyDefinition::class, $configuration->getDefinitionProvider('console'));
    }
}
