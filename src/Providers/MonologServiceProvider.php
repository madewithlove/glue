<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Madewithlove\Glue\Configuration\ConfigurationInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class MonologServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        LoggerInterface::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $this->container->share(LoggerInterface::class, function () {
            /** @var ConfigurationInterface $configuration */
            $configuration = $this->container->get(ConfigurationInterface::class);
            $configuration = $configuration->getPackageConfiguration(__CLASS__);

            $logger = new Logger('app');
            $path   = $configuration['path'].DS.$configuration['filename'];
            $logger->pushHandler(new StreamHandler($path, Logger::WARNING));

            return $logger;
        });
    }
}
