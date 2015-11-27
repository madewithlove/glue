<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Http\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class RequestServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        ServerRequestInterface::class,
        RequestInterface::class,
        ResponseInterface::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $this->container->share(ServerRequestInterface::class, function () {
            return ServerRequestFactory::fromGlobals();
        });

        $this->container->add(RequestInterface::class, function () {
            return $this->container->get(ServerRequestInterface::class);
        });

        $this->container->share(ResponseInterface::class, function () {
            return new Response();
        });
    }
}
