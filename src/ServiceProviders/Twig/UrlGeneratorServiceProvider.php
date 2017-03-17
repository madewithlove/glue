<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\ServiceProviders\Twig;

use Interop\Container\ServiceProviderInterface;
use Madewithlove\Glue\Services\UrlGenerator;
use Psr\Container\ContainerInterface;
use Twig_Environment;
use Twig_SimpleFunction;

class UrlGeneratorServiceProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected $namespace;

    /**
     * @param string|null $namespace
     */
    public function __construct($namespace = null)
    {
        $this->namespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function getServices()
    {
        return [
            UrlGenerator::class => [$this, 'getGenerator'],
            Twig_Environment::class => [$this, 'withUrlGenerator'],
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return UrlGenerator
     */
    public function getGenerator(ContainerInterface $container)
    {
        return new UrlGenerator($this->namespace, $container->get('routes'));
    }

    /**
     * @param ContainerInterface $container
     * @param callable|null      $getPrevious
     *
     * @return Twig_Environment
     */
    public function withUrlGenerator(ContainerInterface $container, callable $getPrevious = null)
    {
        /** @var Twig_Environment $twig */
        $twig = $getPrevious();
        $twig->addFunction(new Twig_SimpleFunction('url', function ($action, $parameters = []) use ($container) {
            return $container->get(UrlGenerator::class)->to($action, $parameters);
        }));

        return $twig;
    }
}
