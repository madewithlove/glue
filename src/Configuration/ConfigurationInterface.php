<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Configuration;

interface ConfigurationInterface
{
    /**
     */
    public function configure();

    /**
     * @return array
     */
    public function toArray();
}
