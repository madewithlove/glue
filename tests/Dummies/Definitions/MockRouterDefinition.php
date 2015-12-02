<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Dummies\Definitions;

use Assembly\ParameterDefinition;
use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;
use Mockery;

class MockRouterDefinition implements DefinitionProviderInterface
{
    /**
     * Returns the definition to register in the container.
     *
     * @return DefinitionInterface[]
     */
    public function getDefinitions()
    {
        $router = Mockery::mock('Router');
        $router->shouldReceive('get')->once()->andReturnUsing(function ($route) {
            return $route;
        });

        return [
            'router' => new ParameterDefinition($router),
        ];
    }
}
