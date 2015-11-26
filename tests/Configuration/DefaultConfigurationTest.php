<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Configuration;

use Madewithlove\Glue\Http\Providers\TwigServiceProvider;
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

        $configuration->setDebug(true);
        $configuration->configure();
        $this->assertContains(DebugBar::class, $configuration->getMiddlewares());

        $configuration->setDebug(false);
        $configuration->configure();
        $this->assertNotContains(DebugBar::class, $configuration->getMiddlewares());
    }

    public function testCanPickCorrectProvidersStack()
    {
        $configuration = new DefaultConfiguration();

        $configuration->setDebug(true);
        $configuration->configure();
        $this->assertArrayHasKey('debugbar', $configuration->getProviders());

        $configuration->setDebug(false);
        $configuration->configure();
        $this->assertArrayNotHasKey('debugbar', $configuration->getProviders());
    }

    public function testCanProperlySerialize()
    {
        $configuration = new DefaultConfiguration();

        $configuration->setDebug(false);
        $configuration->configure();
        $this->assertArrayNotHasKey('debugbar', $configuration->toArray()['providers']);
    }

    public function testCanOverrideProperties()
    {
        $configuration = new DefaultConfiguration();
        $configuration->setPaths(['foobar']);
        $configuration->setDebug(false);

        $this->assertEquals(['foobar'], $configuration->getPaths());
    }

    public function testCanSetPackageConfiguration()
    {
        $configuration = new DefaultConfiguration();
        $configuration->setPackagesConfiguration([]);
        $configuration->setPackageConfiguration(TwigServiceProvider::class, [
           'foo' => 'bar',
        ]);

        $this->assertEquals([TwigServiceProvider::class => ['foo' => 'bar']], $configuration->getPackagesConfiguration());
    }
}
