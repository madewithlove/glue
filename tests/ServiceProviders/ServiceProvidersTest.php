<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\ServiceProviders;

use Madewithlove\Glue\Container;
use Madewithlove\Glue\Glue;
use Madewithlove\Glue\ServiceProviders\Console\PhinxServiceProvider;
use Madewithlove\Glue\ServiceProviders\Twig\UrlGeneratorServiceProvider;
use Madewithlove\Glue\ServiceProviders\Twig\WebpackServiceProvider;
use Madewithlove\Glue\TestCase;
use Madewithlove\ServiceProviders\Console\SymfonyConsoleServiceProvider;
use Madewithlove\ServiceProviders\Templating\TwigServiceProvider;
use Symfony\Component\Console\Application;
use Twig_Environment;

class ServiceProvidersTest extends TestCase
{
    public function testCanAddPhinxCommands()
    {
        $container = new Container();
        $container->addServiceProvider(new SymfonyConsoleServiceProvider());
        $container->addServiceProvider(new PhinxServiceProvider());

        /** @var Application $console */
        $console = $container->get(Application::class);
        $this->assertTrue($console->has('migrate:migrate'));
    }

    public function testCanAddUrlGeneratorToTwig()
    {
        $container = new Container();
        $container->addServiceProvider(new TwigServiceProvider());
        $container->addServiceProvider(new UrlGeneratorServiceProvider());

        /** @var Twig_Environment $twig */
        $twig = $container->get(Twig_Environment::class);
        $this->assertArrayHasKey('url', $twig->getFunctions());
    }

    public function testCanAddWebpackAssets()
    {
        $container = new Container();
        $container->addServiceProvider(new TwigServiceProvider());
        $container->addServiceProvider(new WebpackServiceProvider(__DIR__));

        /** @var Twig_Environment $twig */
        $twig = $container->get(Twig_Environment::class);
        $this->assertEquals(['foo' => 'bar'], $twig->getGlobals()['assets']);
    }
}
