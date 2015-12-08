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
use Interop\Container\Definition\DefinitionProviderInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
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
     * @param string|null $path
     * @param string      $filename
     */
    public function __construct($path = null, $filename = 'glue.log')
    {
        $this->path = $path ?: getcwd();
        $this->filename = $filename;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitions()
    {
        $handler = new ObjectDefinition(RotatingFileHandler::class);
        $handler->setConstructorArguments($this->path.DS.$this->filename, 0, Logger::WARNING);

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
