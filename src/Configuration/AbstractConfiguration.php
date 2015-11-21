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
            'commands'    => $this->getConsoleCommands(),
            'namespace'   => $this->getNamespace(),
            'debug'       => $this->isDebug(),
            'providers'   => $providers,
            'middlewares' => $this->getMiddlewares(),
            'paths'       => $this->getPaths(),
        ];
    }

    /**
     * @return string
     */
    protected function getNamespace()
    {
        $composer = $this->container->get('paths.root').'/composer.json';
        $composer = file_get_contents($composer);
        $composer = json_decode($composer, true);

        $namespaces = array_keys($composer['autoload']['psr-4']);
        $namespace  = trim($namespaces[0], '\\');

        return $namespace;
    }
}
