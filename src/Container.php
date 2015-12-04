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
use Interop\Container\Definition\ReferenceDefinitionInterface;
use League\Container\Container as LeagueContainer;
use League\Container\ContainerAwareInterface;
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
            $this->shared[$alias] = $this->resolve($alias, $this->interopDefinitions[$alias]);
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
        if ($provider instanceof ContainerAwareInterface) {
            $provider->setContainer($this);
        }

        foreach ($provider->getDefinitions() as $identifier => $definition) {
            if ($definition instanceof ExtendDefinitionInterface) {
                $this->extensions[$definition->getExtended()][] = $definition;
            } else {
                $this->interopDefinitions[$identifier] = $definition;
            }
        }
    }

    /**
     * @param string              $identifier
     * @param DefinitionInterface $definition
     *
     * @throws InvalidDefinition
     * @throws UnsupportedDefinition
     *
     * @return mixed
     */
    private function resolve($identifier, DefinitionInterface $definition)
    {
        $resolver = new DefinitionResolver($this);
        $service = $resolver->resolve($definition);

        // Add extensions
        if (array_key_exists($identifier, $this->extensions)) {
            foreach ($this->extensions[$identifier] as $extension) {
                $service = $this->callAssignments($service, $extension);
                $service = $this->callMethods($service, $extension);
            }
        }

        return $service;
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
            $methodArguments = array_map([$this, 'resolveSubDefinition'], $methodArguments);
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
            $service->$propertyName = $this->resolveSubDefinition($propertyAssignment->getValue());
        }

        return $service;
    }

    /**
     * Resolve a variable that can be a reference.
     *
     * @param ReferenceDefinitionInterface|mixed $value
     *
     * @return mixed
     */
    private function resolveSubDefinition($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'resolveSubDefinition'], $value);
        } elseif ($value instanceof DefinitionInterface) {
            return (new DefinitionResolver($this))->resolve($value);
        }

        return $value;
    }
}
