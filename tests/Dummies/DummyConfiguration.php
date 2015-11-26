<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Dummies;

use Madewithlove\Glue\Configuration\AbstractConfiguration;

class DummyConfiguration extends AbstractConfiguration
{
    public function __construct(array $attributes = [])
    {
        parent::__construct(array_merge([
            'debug' => true,
            'providers' => [
                'foo' => 'foo',
                'bar' => 'bar',
            ],
            'middlewares' => [
                'foo',
                'bar',
            ],
        ], $attributes));
    }
}
