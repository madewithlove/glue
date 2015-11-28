<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue;

use Assembly\Container\InvalidDefinition;
use Assembly\Container\UnsupportedDefinition;
use Interop\Container\Definition\AliasDefinitionInterface;
use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;
use Interop\Container\Definition\FactoryCallDefinitionInterface;
use Interop\Container\Definition\ObjectDefinitionInterface;
use Interop\Container\Definition\ParameterDefinitionInterface;
use Interop\Container\Definition\ReferenceInterface;
use League\Container\Container as LeagueContainer;
use ReflectionClass;

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
     * @param string $alias
     * @param array  $args
     *
     * @return mixed|object
     */
    public function get($alias, array $args = [])
    {
        if (array_key_exists($alias, $this->interopDefinitions)) {
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
            $this->interopDefinitions[$definition->getIdentifier()] = $definition;
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
        switch (true) {
            case $definition instanceof ParameterDefinitionInterface:
                return $definition->getValue();

            case $definition instanceof ObjectDefinitionInterface:
                $reflection = new ReflectionClass($definition->getClassName());

                // Create the instance
                $constructorArguments = $definition->getConstructorArguments();
                $constructorArguments = array_map([$this, 'resolveReference'], $constructorArguments);
                $service = $reflection->newInstanceArgs($constructorArguments);

                // Set properties
                foreach ($definition->getPropertyAssignments() as $propertyAssignment) {
                    $propertyName = $propertyAssignment->getPropertyName();
                    $service->$propertyName = $this->resolveReference($propertyAssignment->getValue());
                }

                // Call methods
                foreach ($definition->getMethodCalls() as $methodCall) {
                    $methodArguments = $methodCall->getArguments();
                    $methodArguments = array_map([$this, 'resolveReference'], $methodArguments);
                    call_user_func_array([$service, $methodCall->getMethodName()], $methodArguments);
                }

                return $service;
            case $definition instanceof AliasDefinitionInterface:
                return $this->get($definition->getTarget());

            case $definition instanceof FactoryCallDefinitionInterface:
                $factory = $definition->getFactory();
                $methodName = $definition->getMethodName();
                $arguments = $definition->getArguments();

                if (is_string($factory)) {
                    return $factory::$methodName(...$arguments);
                } elseif ($factory instanceof ReferenceInterface) {
                    $factory = $this->get($factory->getTarget());

                    return $factory->$methodName(...$arguments);
                }

                throw new InvalidDefinition(sprintf('Definition "%s" does not return a valid factory'));

            default:
                throw UnsupportedDefinition::fromDefinition($definition);
        }
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
}
