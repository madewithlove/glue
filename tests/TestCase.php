<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue;

use Mockery;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Define constants and stuff.
     */
    public function setUp()
    {
        new Glue();
    }

    /**
     * Tear down the tests.
     */
    public function tearDown()
    {
        Mockery::close();
    }
}
