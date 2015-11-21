<?php

namespace Madewithlove\Nanoframework\Http\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequestFactory;

class RequestServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        ServerRequestInterface::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $this->container->share(ServerRequestInterface::class, function () {
            return ServerRequestFactory::fromGlobals(
                $_SERVER,
                $_GET,
                $_POST,
                $_COOKIE,
                $_FILES
            );
        });
    }
}
