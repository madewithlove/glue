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
use Assembly\Reference;
use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class MonologDefinition implements DefinitionProviderInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @param string $path
     * @param string $filename
     */
    public function __construct($path, $filename)
    {
        $this->path = $path;
        $this->filename = $filename;
    }

    /**
     * Returns the definition to register in the container.
     *
     * @return DefinitionInterface[]
     */
    public function getDefinitions()
    {
        $handler = new ObjectDefinition(StreamHandler::class);
        $handler->setConstructorArguments($this->path.DS.$this->filename, Logger::WARNING);

        $logger = new ObjectDefinition(Logger::class);
        $logger->setConstructorArguments('glue');
        $logger->addMethodCall('pushHandler', new Reference(HandlerInterface::class));

        return [
            LoggerInterface::class => $logger,
            HandlerInterface::class => $handler,
            'monolog' => new Reference(LoggerInterface::class),
        ];
    }
}
