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
use Assembly\ObjectDefinition;
use Assembly\Reference;
use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class MonologDefinition implements DefinitionProviderInterface
{
    /**
     * @var array
     */
    protected $options = [
        'path' => '',
        'filename' => '',
    ];

    /**
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
        $handler = new ObjectDefinition('monolog.handler', StreamHandler::class);
        $handler->setConstructorArguments($this->options['path'].DS.$this->options['filename'], Logger::WARNING);

        $logger = new ObjectDefinition(LoggerInterface::class, Logger::class);
        $logger->setConstructorArguments('glue');
        $logger->addMethodCall('pushHandler', new Reference('monolog.handler'));

        return [
            LoggerInterface::class => $logger,
            'monolog.handler' => $handler,
            'monolog' => new AliasDefinition('monolog', LoggerInterface::class),
        ];
    }
}