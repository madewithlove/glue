<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions;

use Assembly\AliasDefinition;
use Assembly\FactoryCallDefinition;
use Assembly\ObjectDefinition;
use Assembly\Reference;
use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;

class FlysystemDefinition implements DefinitionProviderInterface
{
    /**
     * @var array
     */
    protected $options = [
        'adapters' => [],
        'default' => [],
    ];

    /**
     * FlysystemDefinition constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
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
        // Wrap adapters in Filesystem instances
        foreach ($this->options['adapters'] as &$adapter) {
            $adapter = new Filesystem($adapter);
        }

        $mountManager = new ObjectDefinition(MountManager::class, MountManager::class);
        $mountManager->setConstructorArguments($this->options['adapters']);

        $default = new FactoryCallDefinition(FilesystemInterface::class, new Reference('flysystem.mount-manager'), 'getFilesystem');
        $default->setArguments($this->options['default']);

        return [
            MountManager::class => $mountManager,
            FilesystemInterface::class => $default,
            'flysystem.mount-manager' => new AliasDefinition('flysystem.mount-manager', MountManager::class),
            'flysystem' => new AliasDefinition('flysystem', FilesystemInterface::class),
        ];
    }
}
