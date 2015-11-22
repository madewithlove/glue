<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Dummies;

use League\Tactician\CommandBus;
use Madewithlove\Glue\Traits\DispatchesCommands;
use Psr\Http\Message\ServerRequestInterface;

class DummyController
{
    use DispatchesCommands;

    /**
     * @var CommandBus
     */
    protected $bus;

    /**
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @return CommandBus
     */
    protected function getCommandBus()
    {
        return $this->bus;
    }

    public function index()
    {
        return $this->dispatch(DummyCommand::class, ['foobar' => 'foobar']);
    }

    public function create()
    {
        return $this->dispatch(new DummyCommand('foobar'));
    }

    public function show(ServerRequestInterface $request)
    {
        return $this->dispatchFromRequest(DummyCommand::class, $request);
    }
}
