<?php
namespace Madewithlove\Glue\Configuration;

use Madewithlove\Glue\TestCase;
use Psr7Middlewares\Middleware\DebugBar;

class DefaultConfigurationTest extends TestCase
{
    public function testCanInfereNamespace()
    {
        $configuration = new DefaultConfiguration();
        $this->assertEquals('Madewithlove\\Glue', $configuration->namespace);
    }

    public function testCanInfereRootPath()
    {
        $configuration = new DefaultConfiguration();
        $this->assertEquals(realpath(__DIR__.'/../..'), $configuration->getRootPath());
    }

    public function testCanPickCorrectMiddlewareStack()
    {
        $configuration = new DefaultConfiguration();

        $configuration->debug = true;
        $this->assertContains(DebugBar::class, $configuration->getMiddlewares());

        $configuration->debug = false;
        $this->assertNotContains(DebugBar::class, $configuration->getMiddlewares());
    }

    public function testCanPickCorrectProvidersStack()
    {
        $configuration = new DefaultConfiguration();

        $configuration->debug = true;
        $this->assertArrayHasKey('debugbar', $configuration->getProviders());

        $configuration->debug = false;
        $this->assertArrayNotHasKey('debugbar', $configuration->getProviders());
    }

    public function testCanProperlySerialize()
    {
        $configuration = new DefaultConfiguration();

        $configuration->debug = false;
        $this->assertArrayNotHasKey('debugbar', $configuration->toArray()['providers']);
    }
}
