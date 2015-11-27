<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Http\Middlewares;

use League\Route\RouteCollection;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Relay\MiddlewareInterface;

class LeagueRouteMiddleware
{
    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * LeagueRouteMiddleware constructor.
     *
     * @param RouteCollection $routes
     */
    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @param ServerRequestInterface            $request
     * @param Response                          $response
     * @param callable|MiddlewareInterface|null $next
     *
     * @return Response
     */
    public function __invoke(ServerRequestInterface $request, Response $response, callable $next = null)
    {
        $response = $this->routes->dispatch($request, $response);

        return $next($request, $response);
    }
}
