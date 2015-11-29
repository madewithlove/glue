<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions;

use Assembly\AliasDefinition;
use Assembly\FactoryCallDefinition;
use Assembly\ObjectDefinition;
use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class ZendDiactorosDefinition implements DefinitionProviderInterface
{
    /**
     * Returns the definition to register in the container.
     *
     * @return DefinitionInterface[]
     */
    public function getDefinitions()
    {
        $request = new FactoryCallDefinition(ServerRequestInterface::class, ServerRequestFactory::class, 'fromGlobals');
        $response = new ObjectDefinition(ResponseInterface::class, Response::class);

        return [
            ServerRequestInterface::class => $request,
            ResponseInterface::class => $response,
            RequestInterface::class => new AliasDefinition(RequestInterface::class, ServerRequestInterface::class),
        ];
    }
}
