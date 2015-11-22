<?php

namespace Madewithlove\Glue\Http\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Madewithlove\Glue\Services\UrlGenerator;
use Twig_Environment;
use Twig_SimpleFunction;

class UrlGeneratorServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    /**
     * @var array
     */
    protected $provides = [
        UrlGenerator::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $this->container->share(UrlGenerator::class, function () {
            return new UrlGenerator(
                $this->container->get('config.namespace'),
                $this->container->get('routes')
            );
        });
    }

    /**
     * Method will be invoked on registration of a service provider implementing
     * this interface. Provides ability for eager loading of Service Providers.
     */
    public function boot()
    {
        $twig = $this->container->get(Twig_Environment::class);
        $twig->addFunction(new Twig_SimpleFunction('url', function ($action, $parameters = []) {
            return $this->container->get(UrlGenerator::class)->to($action, $parameters);
        }));
    }
}
