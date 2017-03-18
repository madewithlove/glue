<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Configuration;

use Madewithlove\Glue\Dummies\ServiceProviders\DummyServiceProvider;
use Madewithlove\Glue\TestCase;

class AbstractConfigurationTest extends TestCase
{
    public function testCanOverrideParticularProvider()
    {
        $configuration = new DefaultConfiguration();
        $configuration->setServiceProvider('console', new DummyServiceProvider());

        $this->assertInstanceOf(DummyServiceProvider::class, $configuration->getServiceProvider('console'));
    }
}
