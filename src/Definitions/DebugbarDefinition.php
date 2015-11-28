<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions;

use Assembly\FactoryCallDefinition;
use Assembly\ObjectDefinition;
use Assembly\Reference;
use DebugBar\DebugBar;
use DebugBar\JavascriptRenderer;
use DebugBar\StandardDebugBar;
use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;

class DebugbarDefinition implements DefinitionProviderInterface
{
    /**
     * Returns the definition to register in the container.
     *
     * @return DefinitionInterface[]
     */
    public function getDefinitions()
    {
        $debugbar = new ObjectDefinition(DebugBar::class, StandardDebugBar::class);
        $renderer = new FactoryCallDefinition(
            JavascriptRenderer::class,
            new Reference(DebugBar::class),
            'getJavascriptRenderer'
        );

        return [
            DebugBar::class => $debugbar,
            JavascriptRenderer::class => $renderer,
        ];
    }
}
