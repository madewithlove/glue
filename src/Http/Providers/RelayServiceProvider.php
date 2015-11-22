<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Http\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Madewithlove\Glue\Configuration\ConfigurationInterface;
use Relay\Relay;
use Relay\RelayBuilder;

class RelayServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        Relay::class,
        'pipeline',
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $this->container->share(Relay::class, function () {
            $builder = new RelayBuilder(function ($callable) {
                return is_string($callable) ? $this->container->get($callable) : $callable;
            });

            // Process middlewares
            $middlewares = $this->container->get(ConfigurationInterface::class)->getMiddlewares();
            $relay = $builder->newInstance($middlewares);

            return $relay;
        });

        $this->container->add('pipeline', function () {
            return $this->container->get(Relay::class);
        });
    }
}
