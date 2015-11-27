<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Dummies\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;

class FirstProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        'foo',
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $this->container->add('foo', function () {
            echo 1;
        });
    }
}
