<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

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
