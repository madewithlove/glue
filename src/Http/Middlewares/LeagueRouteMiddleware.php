<?php

namespace Madewithlove\Glue\Http\Middlewares;

use League\Route\RouteCollection;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Relay\MiddlewareInterface;

class LeagueRouteMiddleware implements MiddlewareInterface
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
     * @param Request                           $request
     * @param Response                          $response
     * @param callable|MiddlewareInterface|null $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        $response = $this->routes->dispatch($request, $response);

        return $next($request, $response);
    }
}
