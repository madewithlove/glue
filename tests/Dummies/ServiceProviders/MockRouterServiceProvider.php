<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Dummies\ServiceProviders;

use Interop\Container\ServiceProviderInterface;
use Madewithlove\ServiceProviders\Utilities\Parameter;
use Mockery;

class MockRouterServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getServices()
    {
        $router = Mockery::mock('Router');
        $router->shouldReceive('get')->once()->andReturnUsing(function ($route) {
            return $route;
        });

        return [
            'router' => new Parameter($router),
        ];
    }
}
