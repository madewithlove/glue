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
use Twig_Environment;
use Twig_Extension;
use Twig_Loader_Array;
use Twig_Loader_Filesystem;
use Twig_LoaderInterface;

class TwigDefinition implements DefinitionProviderInterface
{
    /**
     * Path to the views or a list of views.
     *
     * @var string|string[]
     */
    protected $views;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var Twig_Extension[]
     */
    protected $extensions = [];

    /**
     * @param string|string[]  $views
     * @param array            $options
     * @param Twig_Extension[] $extensions
     */
    public function __construct($views, array $options = [], array $extensions = [])
    {
        $this->views = $views;
        $this->options = $options;
        $this->extensions = $extensions;
    }

    /**
     * Returns the definition to register in the container.
     *
     * @return DefinitionInterface[]
     */
    public function getDefinitions()
    {
        $isViewsFolder = is_string($this->views) && is_dir($this->views);

        // Define loader
        $loader = $isViewsFolder ? Twig_Loader_Filesystem::class : Twig_Loader_Array::class;
        $loader = new ObjectDefinition($loader);
        $loader->setConstructorArguments($this->views ?: []);

        $twig = new ObjectDefinition(Twig_Environment::class);
        $twig->setConstructorArguments(new Reference(Twig_LoaderInterface::class), $this->options);

        foreach ($this->extensions as $extension) {
            $twig->addMethodCall('addExtension', new Reference($extension));
        }

        return [
            Twig_LoaderInterface::class => $loader,
            Twig_Environment::class => $twig,
            'twig' => new Reference(Twig_Environment::class),
        ];
    }
}
