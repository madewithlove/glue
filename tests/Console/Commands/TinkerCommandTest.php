<?php
namespace Madewithlove\Glue\Console\Commands;

use League\Container\Container;
use Madewithlove\Glue\Configuration\ConfigurationInterface;
use Madewithlove\Glue\TestCase;
use Mockery;
use Psy\Shell;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class TinkerCommandTest extends TestCase
{
    public function testCanStartRepl()
    {
        $container = new Container();
        $container->add(ConfigurationInterface::class, 'foobar');

        $repl = Mockery::mock(Shell::class);
        $repl->shouldReceive('run')->once();
        $repl->shouldReceive('setScopeVariables')->once()->with([
            'app' => $container,
            'config'    => 'foobar',
        ]);

        $tinker = new TinkerCommand($container, $repl);
        $tinker->run(new ArrayInput([]), new NullOutput());
    }
}
