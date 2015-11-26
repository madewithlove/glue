<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Providers;

class PathsServiceProvider extends AbstractValuesProvider
{
    /**
     * @var string
     */
    protected $key = 'paths';

    /**
     * @return array
     */
    protected function getValues()
    {
        return $this->container->has('config.paths')
            ? $this->container->get('config.paths')
            : [];
    }
}
