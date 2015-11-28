<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Console;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Madewithlove\Glue\Configuration\ConfigurationInterface;
use Phinx\Config\Config;
use Phinx\Console\Command;
use Symfony\Component\Console\Application;

class PhinxServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        // ...
    }

    /**
     * Boot the provider.
     */
    public function boot()
    {
        /** @var Application $console */
        $console = $this->container->get(Application::class);
        $console->addCommands([
        ]);
    }
}
