<?php

namespace Madewithlove\Nanoframework\Http\Controllers;

use League\Tactician\CommandBus;
use Madewithlove\Nanoframework\Traits\DispatchesCommands;
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
        $this->twig       = $twig;
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
