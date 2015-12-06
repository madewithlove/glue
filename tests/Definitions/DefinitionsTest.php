<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions;

use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;
use League\Flysystem\Adapter\Local;
use Madewithlove\Glue\Configuration\DefaultConfiguration;
use Madewithlove\Glue\Container;
use Madewithlove\Glue\Definitions\Console\PhinxDefinition;
use Madewithlove\Glue\Definitions\Console\SymfonyConsoleDefinition;
use Madewithlove\Glue\Definitions\DefinitionTypes\ExtendDefinition;
use Madewithlove\Glue\Definitions\Glue\ConfigurationDefinition;
use Madewithlove\Glue\Definitions\Glue\PathsDefinition;
use Madewithlove\Glue\Definitions\Twig\TwigDefinition;
use Madewithlove\Glue\Definitions\Twig\UrlGeneratorDefinition;
use Madewithlove\Glue\Definitions\Twig\WebpackDefinition;
use Madewithlove\Glue\Glue;
use Madewithlove\Glue\TestCase;
use Symfony\Component\Console\Application;
use Twig_Environment;

class DefinitionsTest extends TestCase
{
    /**
     * @dataProvider provideDefinitions
     *
     * @param DefinitionProviderInterface $provider
     */
    public function testCanResolveDefinition(DefinitionProviderInterface $provider)
    {
        $container = new Container();
        $container->addDefinitionProvider($provider);

        $definitions = $provider->getDefinitions();
        foreach ($definitions as $key => $definition) {
            $service = $container->get($key);
            if (!$definition instanceof ExtendDefinition) {
                $this->assertNotInstanceOf(DefinitionInterface::class, $service);
            }
        }
    }

    public function testCanAddPhinxCommands()
    {
        $container = new Container();
        $container->addDefinitionProvider(new SymfonyConsoleDefinition());
        $container->addDefinitionProvider(new PhinxDefinition());

        /** @var Application $console */
        $console = $container->get(Application::class);
        $this->assertTrue($console->has('migrate:migrate'));
    }

    public function testCanAddUrlGeneratorToTwig()
    {
        $container = new Container();
        $container->addDefinitionProvider(new TwigDefinition());
        $container->addDefinitionProvider(new UrlGeneratorDefinition());

        /** @var Twig_Environment $twig */
        $twig = $container->get(Twig_Environment::class);
        $this->assertArrayHasKey('url', $twig->getFunctions());
    }

    public function testCanAddWebpackAssets()
    {
        $container = new Container();
        $container->addDefinitionProvider(new TwigDefinition());
        $container->addDefinitionProvider(new WebpackDefinition(__DIR__));

        /** @var Twig_Environment $twig */
        $twig = $container->get(Twig_Environment::class);
        $this->assertEquals(['foo' => 'bar'], $twig->getGlobals()['assets']);
    }

    /**
     * @return array
     */
    public function provideDefinitions()
    {
        $glue = new Glue();

        return [
            [new PathsDefinition(new DefaultConfiguration())],
            [new ConfigurationDefinition(new DefaultConfiguration())],
            [new FlysystemDefinition('local', ['local' => new Local(__DIR__)])],
            [new DebugbarDefinition()],
            [new LeagueRouteDefinition()],
            [new MonologDefinition()],
            [new RelayDefinition()],
            [new TacticianDefinition()],
            [new ZendDiactorosDefinition()],
            [new SymfonyConsoleDefinition()],
            [new TwigDefinition()],
        ];
    }
}
