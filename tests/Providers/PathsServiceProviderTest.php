<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Providers;

use Madewithlove\Glue\Configuration\Configuration;
use Madewithlove\Glue\Glue;
use Madewithlove\Glue\TestCase;

class PathsServiceProviderTest extends TestCase
{
    public function testCanBindPaths()
    {
        $glue = new Glue(new Configuration([
            'providers' => [
            ],
            'paths' => [
                'foo' => 'bar',
            ],
        ]));

        $glue->boot();
        $this->assertEquals('bar', $glue->getContainer()->get('paths.foo'));
    }
}
