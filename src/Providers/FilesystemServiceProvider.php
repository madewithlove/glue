<?php
namespace Madewithlove\Glue\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Madewithlove\Glue\Configuration\ConfigurationInterface;

class FilesystemServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        FilesystemInterface::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $this->container->share(FilesystemInterface::class, function () {
            $configuration = $this->container->get(ConfigurationInterface::class);
            $adapter       = new Local($configuration->rootPath);

            return new Filesystem($adapter);
        });
    }
}