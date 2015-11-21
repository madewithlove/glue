<?php

namespace Madewithlove\Nanoframework\Configuration;

use Interop\Container\ContainerInterface;

abstract class AbstractConfiguration implements ConfigurationInterface
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        $providers = array_values($this->getProviders());
        if ($this->isDebug()) {
            $providers = array_merge($providers, $this->getDebugProviders());
        }

        return [
            'debug'       => $this->isDebug(),
            'providers'   => $providers,
            'middlewares' => $this->getMiddlewares(),
            'paths'       => $this->getPaths(),
        ];
    }
}
