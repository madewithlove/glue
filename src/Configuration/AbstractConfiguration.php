<?php

namespace Madewithlove\Glue\Configuration;

use Illuminate\Support\Fluent;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;

/**
 * @property $namespace   The namespace of your application
 * @property $rootPath    The path to the root of your application
 * @property $debug       Whether we're in debug mode or not
 * @property $providers   The providers to apply
 * @property $middlewares The middlewares to apply to the current route
 * @property $commands    The commands to register with the CLI
 * @property $paths       The paths in your application
 */
abstract class AbstractConfiguration extends Fluent implements ConfigurationInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Setup the configuration.
     */
    public function configure()
    {
        // ...
    }
}
