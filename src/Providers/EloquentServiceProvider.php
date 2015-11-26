<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Providers;

use Illuminate\Database\Capsule\Manager;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Madewithlove\Glue\Configuration\ConfigurationInterface;

class EloquentServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    /**
     * @var array
     */
    protected $provides = [
        Manager::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $this->container->share(Manager::class, function () {
            $configuration = $this->container->get(ConfigurationInterface::class);
            $configuration = $configuration->getPackageConfiguration(__CLASS__);

            $capsule = new Manager();
            foreach ($configuration['connections'] as $name => $connection) {
                $capsule->addConnection($connection, $name);
            }

            // Configure database capsule
            $capsule->bootEloquent();
            $capsule->setAsGlobal();

            return $capsule;
        });
    }

    /**
     * Method will be invoked on registration of a service provider implementing
     * this interface. Provides ability for eager loading of Service Providers.
     */
    public function boot()
    {
        $this->register();
        $this->container->get(Manager::class);
    }
}
