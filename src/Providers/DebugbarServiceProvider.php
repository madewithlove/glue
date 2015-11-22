<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Providers;

use Barryvdh\Debugbar\DataCollector\QueryCollector;
use DebugBar\DebugBar;
use DebugBar\JavascriptRenderer;
use DebugBar\StandardDebugBar;
use Illuminate\Database\Capsule\Manager;
use League\Container\ServiceProvider\AbstractServiceProvider;

class DebugbarServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        DebugBar::class,
        JavascriptRenderer::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $this->container->share(DebugBar::class, function () {
            return new StandardDebugBar();
        });

        $this->container->share(JavascriptRenderer::class, function () {
            return $this->container->get(DebugBar::class)->getJavascriptRenderer();
        });
    }
}
