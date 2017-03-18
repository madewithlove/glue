<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\ServiceProviders\Console;

use Glue\Console\Commands\ServeCommand;
use Madewithlove\Glue\Console\Commands\BootstrapCommand;
use Madewithlove\Glue\Console\Commands\ConfigurationCommand;
use Madewithlove\Glue\Console\Commands\TinkerCommand;
use Madewithlove\Glue\Glue;
use Symfony\Component\Console\Command\Command;

class SymfonyConsoleServiceProvider extends \Madewithlove\ServiceProviders\Console\SymfonyConsoleServiceProvider
{
    /**
     * @var Command[]|string[]
     */
    protected $commands;

    /**
     * @param string[]|Command[] $commands
     */
    public function __construct($commands = [])
    {
        parent::__construct('Glue', Glue::VERSION, $commands);
    }

    /**
     * Create a new instance of SymfonyConsoleDefintion
     * with Glue's default commands.
     *
     * @param array $commands
     *
     * @return SymfonyConsoleServiceProvider
     */
    public static function withDefaultCommands(array $commands = [])
    {
        return new self(array_merge($commands, [
            BootstrapCommand::class,
            TinkerCommand::class,
            ServeCommand::class,
            ConfigurationCommand::class,
        ]));
    }
}
