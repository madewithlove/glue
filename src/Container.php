<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue;

use Interop\Container\ServiceProviderInterface;
use League\Container\Container as LeagueContainer;
use Madewithlove\ServiceProviders\Bridges\LeagueContainerDecorator;

/**
 * A definition-interop compatible version of league/container.
 */
class Container extends LeagueContainer
{
    /**
     * @param ServiceProviderInterface|\League\Container\ServiceProvider\ServiceProviderInterface|string $provider
     *
     * @return $this|void
     */
    public function addServiceProvider($provider)
    {
        if ($provider instanceof ServiceProviderInterface) {
            $provider = new LeagueContainerDecorator($provider);
        }

        return parent::addServiceProvider($provider);
    }
}
