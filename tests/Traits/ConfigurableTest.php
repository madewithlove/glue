<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Traits;

use Madewithlove\Glue\Dummies\DummyConfiguration;
use Madewithlove\Glue\Glue;
use Madewithlove\Glue\TestCase;

class ConfigurableTest extends TestCase
{
    public function testCanProperlyMergeConfiguration()
    {
        $glue = new Glue(new DummyConfiguration());
        $glue->configure([
            'debug' => false,
            'providers' => [
                'foo' => 'baz',
                'qux',
            ],
            'middlewares' => [
                'baz',
            ],
        ]);

        $this->assertEquals([
            'debug' => false,
            'providers' => [
                'foo' => 'baz',
                'bar' => 'bar',
                'qux',
            ],
            'middlewares' => [
                'foo',
                'bar',
                'baz',
            ],
        ], $glue->getConfiguration()->toArray());
    }

    public function testCanOverrideIndividualKeys()
    {
        $glue = new Glue(new DummyConfiguration());
        $glue->configure('providers', [
            'foo' => 'baz',
            'qux',
        ]);

        $this->assertEquals([
            'debug' => true,
            'providers' => [
                'foo' => 'baz',
                'bar' => 'bar',
                'qux',
            ],
            'middlewares' => [
                'foo',
                'bar',
            ],
        ], $glue->getConfiguration()->toArray());
    }
}
