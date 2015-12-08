<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions;

use Assembly\FactoryCallDefinition;
use Assembly\ObjectDefinition;
use Assembly\Reference;
use Interop\Container\Definition\DefinitionProviderInterface;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;

class FlysystemDefinition implements DefinitionProviderInterface
{
    /**
     * @var string
     */
    protected $default;

    /**
     * @var AdapterInterface[]
     */
    protected $adapters = [];

    /**
     * @param string $default
     * @param array  $adapters
     */
    public function __construct($default, array $adapters)
    {
        $this->default = $default;
        $this->adapters = $adapters;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitions()
    {
        $mountManager = new ObjectDefinition(MountManager::class);
        $mountManager->setConstructorArguments(array_map(function (AdapterInterface $adapter) {
            return new Filesystem($adapter);
        }, $this->adapters));

        $default = new FactoryCallDefinition(new Reference('flysystem.mount-manager'), 'getFilesystem');
        $default->setArguments($this->default);

        return [
            MountManager::class => $mountManager,
            FilesystemInterface::class => $default,
            'flysystem.mount-manager' => new Reference(MountManager::class),
            'flysystem' => new Reference(FilesystemInterface::class),
        ];
    }
}
