<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Http\Controllers;

use League\Tactician\CommandBus;
use Madewithlove\Glue\Traits\DispatchesCommands;
use Twig_Environment;
use Zend\Diactoros\Response\HtmlResponse;

abstract class AbstractController
{
    use DispatchesCommands;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var CommandBus
     */
    protected $commandBus;

    /**
     * AbstractController constructor.
     *
     * @param Twig_Environment $twig
     * @param CommandBus       $commandBus
     */
    public function __construct(Twig_Environment $twig, CommandBus $commandBus)
    {
        $this->twig = $twig;
        $this->commandBus = $commandBus;
    }

    /**
     * @param string $view
     * @param array  $data
     *
     * @return HtmlResponse
     */
    protected function render($view, array $data = [])
    {
        return new HtmlResponse($this->twig->render($view, $data));
    }

    /**
     * @return CommandBus
     */
    protected function getCommandBus()
    {
        return $this->commandBus;
    }
}
