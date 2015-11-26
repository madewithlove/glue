<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Madewithlove\Glue\Configuration\ConfigurationInterface;

class FlysystemServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        FilesystemInterface::class,
        MountManager::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        /* @var ConfigurationInterface $configuration */
        $this->container->share(FilesystemInterface::class, function () {
            $configuration = $this->container->get(ConfigurationInterface::class);
            $adapter = new Local($configuration->getRootPath());

            return new Filesystem($adapter);
        });

        $this->container->share(MountManager::class, function () {
            return new MountManager([
                'local' => $this->container->get(FilesystemInterface::class),
            ]);
        });
    }
}
