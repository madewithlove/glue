<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions\Twig;

use Assembly\AliasDefinition;
use Assembly\ObjectDefinition;
use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;
use Twig_Environment;

class TwigDefinition implements DefinitionProviderInterface
{
    /**
     * @var array
     */
    protected $options = [
        'loader' => null,
        'environment' => null,
        'extensions' => [],
    ];

    /**
     * TwigDefinition constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * Returns the definition to register in the container.
     *
     * @return DefinitionInterface[]
     */
    public function getDefinitions()
    {
        $twig = new ObjectDefinition(Twig_Environment::class, Twig_Environment::class);
        $twig->setConstructorArguments($this->options['loader'], $this->options['environment']);

        foreach ($this->options['extensions'] as $extension) {
            $twig->addMethodCall('addExtension', $extension);
        }

        return [
            Twig_Environment::class => $twig,
            'twig' => new AliasDefinition('twig', Twig_Environment::class),
        ];
    }
}
