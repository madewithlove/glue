<?php
namespace Madewithlove\Nanoframework\Configuration;

use League\Container\ContainerAwareTrait;
use League\Route\RouteCollection;

abstract class AbstractConfiguration implements ConfigurationInterface
{
    use ContainerAwareTrait;

    /**
     * @return array
     */
    public function getConfiguration()
    {
        $routes    = $this->container->get(RouteCollection::class);
        $providers = $this->getProviders();
        if ($this->isDebug()) {
            $providers = array_merge($providers, $this->getDebugProviders());
        }

        return [
            'debug'       => $this->isDebug(),
            'providers'   => $providers,
            'middlewares' => $this->getMiddlewares(),
            'paths'       => $this->getPaths(),
            'routes'      => $this->getRoutes($routes),
        ];
    }
}
