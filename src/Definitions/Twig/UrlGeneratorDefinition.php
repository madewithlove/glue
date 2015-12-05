<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions\Twig;

use Assembly\ObjectDefinition;
use Assembly\Reference;
use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Madewithlove\Glue\Definitions\DefinitionTypes\ExtendDefinition;
use Madewithlove\Glue\Services\UrlGenerator;
use Twig_Environment;
use Twig_SimpleFunction;

class UrlGeneratorDefinition implements DefinitionProviderInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

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
     * Returns the definition to register in the container.
     *
     * @return DefinitionInterface[]
     */
    public function getDefinitions()
    {
        $generator = new ObjectDefinition(UrlGenerator::class);
        $generator->setConstructorArguments($this->namespace, new Reference('routes'));

        $function = new ExtendDefinition(Twig_Environment::class);
        $function->addMethodCall('addFunction', new Twig_SimpleFunction('url', function ($action, $parameters = []) {
            return $this->container->get(UrlGenerator::class)->to($action, $parameters);
        }));

        return [
            UrlGenerator::class => $generator,
            $function,
        ];
    }
}
