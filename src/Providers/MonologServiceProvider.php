<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Providers;

use DateTime;
use League\Container\ServiceProvider\AbstractServiceProvider;
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
            $filename = (new DateTime())->format('Y-m-d');
            $path = $this->container->get('paths.logs');
            $path = sprintf('%s/%s.log', $path, $filename);

            $logger = new Logger('app');
            $logger->pushHandler(new StreamHandler($path, Logger::WARNING));

            return $logger;
        });
    }
}
