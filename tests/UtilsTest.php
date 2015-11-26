<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue;

class UtilsTest extends TestCase
{
    public function testCanFindFolder()
    {
        $folder = Utils::find('.gitignore');

        $this->assertEquals(realpath(__DIR__.'/..').'/.gitignore', $folder);
    }
}
