<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Console\Commands;

use League\Flysystem\FilesystemInterface;
use Madewithlove\Glue\Configuration\Configuration;
use Madewithlove\Glue\TestCase;
use Mockery;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class BootstrapCommandTest extends TestCase
{
    public function testCanScaffoldDirectories()
    {
        $filesystem = Mockery::mock(FilesystemInterface::class);
        $filesystem->shouldReceive('has')->once()->with('bar/cache')->andReturn(true);
        $filesystem->shouldReceive('has')->andReturn(false);
        $filesystem->shouldReceive('createDir')->once()->with('web');
        $filesystem->shouldReceive('createDir')->once()->with('foo/migrations');
        $filesystem->shouldReceive('createDir')->never()->with('bar/cache');
        $filesystem->shouldReceive('put')->once()->with('console', Mockery::any());
        $filesystem->shouldReceive('put')->once()->with('web/index.php', Mockery::any());
        $filesystem->shouldReceive('put')->once()->with('.env', Mockery::any());

        $configuration = new Configuration([
            'paths' => [
                'web' => 'web',
                'migrations' => 'foo/migrations',
                'cache' => 'bar/cache',
            ],
        ]);

        $bootstrap = new BootstrapCommand($configuration, $filesystem);
        $bootstrap->run(new ArrayInput([]), new NullOutput());
    }
}
