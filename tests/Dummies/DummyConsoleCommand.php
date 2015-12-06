<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Dummies;

use Symfony\Component\Console\Command\Command;

class DummyConsoleCommand extends Command
{
    protected function configure()
    {
        $this->setName('foobar');
    }
}
