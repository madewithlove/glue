<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Dummies\Definitions;

use Interop\Container\ServiceProviderInterface;
use Madewithlove\ServiceProviders\Utilities\Parameter;

class DummyServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getServices()
    {
        return ['foo' => new Parameter('bar')];
    }
}
