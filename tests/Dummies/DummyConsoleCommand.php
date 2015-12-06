<?php
namespace Madewithlove\Glue\Dummies;

use Symfony\Component\Console\Command\Command;

class DummyConsoleCommand extends Command
{
    protected function configure()
    {
        $this->setName('foobar');
    }
}
