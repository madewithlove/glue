<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions;

use Assembly\ObjectDefinition;
use Illuminate\Database\Capsule\Manager;
use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;

class EloquentDefinition implements DefinitionProviderInterface
{
    /**
     * @var array
     */
    protected $connections = [];

    /**
     * @param array $connections
     */
    public function __construct(array $connections = [])
    {
        $this->connections = $connections;
    }

    /**
     * Returns the definition to register in the container.
     *
     * @return DefinitionInterface[]
     */
    public function getDefinitions()
    {
        $manager = new ObjectDefinition(Manager::class, Manager::class);
        foreach ($this->connections as $name => $connection) {
            $manager->addMethodCall('addConnection', $connection, $name);
        }

        $manager->addMethodCall('bootEloquent');
        $manager->addMethodCall('setAsGlobal');

        return [
            Manager::class => $manager,
        ];
    }
}
