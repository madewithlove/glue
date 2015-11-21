<?php

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
            $debugbar = new StandardDebugBar();
            $debugbar->addCollector(new QueryCollector());

            // Bind QueryCollector to current connection
            /* @var StandardDebugbar $debugbar */
            $connection = $this->container->get(Manager::class)->connection();
            $connection->listen(function ($query, $bindings, $time) use ($debugbar, $connection) {
                $collector = $debugbar->getCollector('queries');
                $collector->addQuery((string) $query, $bindings, $time, $connection);
            });

            return $debugbar;
        });

        $this->container->share(JavascriptRenderer::class, function () {
            return $this->container->get(DebugBar::class)->getJavascriptRenderer();
        });
    }
}
