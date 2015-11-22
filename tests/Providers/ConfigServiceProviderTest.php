<?php
namespace Madewithlove\Glue\Providers;

use Madewithlove\Glue\Configuration\Configuration;
use Madewithlove\Glue\Glue;
use Madewithlove\Glue\TestCase;

class ConfigServiceProviderTest extends TestCase
{
    public function testCanBindConfiguration()
    {
        $glue = new Glue(new Configuration([
            'foo' => 'bar',
        ]));

        $glue->boot();
        $this->assertEquals('bar', $glue->getContainer()->get('config.foo'));
    }
}
