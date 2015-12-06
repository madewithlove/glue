<?php
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
