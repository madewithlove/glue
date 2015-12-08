<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions\Twig;

use League\Container\ReflectionContainer;
use Madewithlove\Definitions\Templating\TwigDefinition;
use Madewithlove\Glue\Container;
use Madewithlove\Glue\TestCase;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Extension_Escaper;
use Twig_Loader_Array;

class TwigDefinitionTest extends TestCase
{
    public function testCanConfigureTwig()
    {
        $definition = new TwigDefinition(
            ['foo', 'bar'],
            [
                'auto_reload' => true,
                'debug' => true,
            ],
            [
                new Twig_Extension_Debug(),
                Twig_Extension_Escaper::class,
            ]
        );

        $container = new Container();
        $container->delegate(new ReflectionContainer());
        $container->addDefinitionProvider($definition);

        /** @var Twig_Environment $twig */
        $twig = $container->get(Twig_Environment::class);

        $extensions = $twig->getExtensions();
        $this->assertArrayHasKey('debug', $extensions);
        $this->assertArrayHasKey('escaper', $extensions);

        $this->assertInstanceOf(Twig_Loader_Array::class, $twig->getLoader());
        $this->assertTrue($twig->isAutoReload());
        $this->assertTrue($twig->isDebug());
    }
}
