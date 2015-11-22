<?php
namespace Madewithlove\Glue\Dummies;

class DummyCommand
{
    public $foobar;

    /**
     * DummyCommand constructor.
     *
     * @param $foobar
     */
    public function __construct($foobar = null)
    {
        $this->foobar = $foobar;
    }
}
