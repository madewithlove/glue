<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue;

use Assembly\Container\DefinitionResolver;
use Assembly\Container\InvalidDefinition;
use Assembly\Container\UnsupportedDefinition;
use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;
use Interop\Container\Definition\ObjectDefinitionInterface;
use Interop\Container\Definition\ReferenceInterface;
use League\Container\Container as LeagueContainer;
use Madewithlove\Glue\Definitions\DefinitionTypes\ExtendDefinitionInterface;

/**
 * A definition-interop compatible version of league/container.
 */
class Container extends LeagueContainer
{
    /**
     * @var DefinitionInterface[]
     */
    protected $interopDefinitions = [];

    /**
     * @var ExtendDefinitionInterface[][]
     */
    protected $extensions = [];

    /**
     * @param string $alias
     * @param array  $args
     *
     * @return mixed|object
     */
    public function get($alias, array $args = [])
    {
        if (!$this->hasShared($alias) && array_key_exists($alias, $this->interopDefinitions)) {
            $this->shared[$alias] = $this->resolveDefinition($this->interopDefinitions[$alias]);
        }

        return parent::get($alias, $args);
    }

    /**
     * @param string $alias
     *
     * @return bool
     */
    public function has($alias)
    {
        if (array_key_exists($alias, $this->interopDefinitions)) {
            return true;
        }

        return parent::has($alias);
    }

    /**
     * @param DefinitionProviderInterface $provider
     */
    public function addDefinitionProvider(DefinitionProviderInterface $provider)
    {
        foreach ($provider->getDefinitions() as $definition) {
            if ($definition instanceof ExtendDefinitionInterface) {
                $this->extensions[$definition->getExtended()][] = $definition;
            } else {
                $this->interopDefinitions[$definition->getIdentifier()] = $definition;
            }
        }
    }

    /**
     * Resolve a definition and return the resulting value.
     *
     * @param DefinitionInterface $definition
     *
     * @throws InvalidDefinition
     * @throws UnsupportedDefinition
     *
     * @return mixed
     */
    private function resolveDefinition(DefinitionInterface $definition)
    {
        $resolver = new DefinitionResolver($this);
        $service = $resolver->resolve($definition);

        // Add extensions
        if (array_key_exists($definition->getIdentifier(), $this->extensions)) {
            foreach ($this->extensions[$definition->getIdentifier()] as $extension) {
                $service = $this->callAssignments($service, $extension);
                $service = $this->callMethods($service, $extension);
            }
        }

        return $service;
    }

    /**
     * Resolve a variable that can be a reference.
     *
     * @param ReferenceInterface|mixed $value
     *
     * @return mixed
     */
    private function resolveReference($value)
    {
        if ($value instanceof ReferenceInterface) {
            $value = $this->get($value->getTarget());
        }

        return $value;
    }

    /**
     * @param object                    $service
     * @param ObjectDefinitionInterface $definition
     *
     * @return object
     */
    private function callMethods($service, ObjectDefinitionInterface $definition)
    {
        foreach ($definition->getMethodCalls() as $methodCall) {
            $methodArguments = $methodCall->getArguments();
            $methodArguments = array_map([$this, 'resolveReference'], $methodArguments);
            call_user_func_array([$service, $methodCall->getMethodName()], $methodArguments);
        }

        return $service;
    }

    /**
     * @param object                    $service
     * @param ObjectDefinitionInterface $definition
     *
     * @return object
     */
    private function callAssignments($service, ObjectDefinitionInterface $definition)
    {
        foreach ($definition->getPropertyAssignments() as $propertyAssignment) {
            $propertyName = $propertyAssignment->getPropertyName();
            $service->$propertyName = $this->resolveReference($propertyAssignment->getValue());
        }

        return $service;
    }
}
