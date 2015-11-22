<?php

namespace Madewithlove\Glue\Http\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Array;
use Twig_Loader_Filesystem;

class TwigServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        Twig_Environment::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $this->container->share(Twig_Environment::class, function () {
            $views = $this->container->get('paths.views');
            $loader = is_dir($views) ? new Twig_Loader_Filesystem($views) : new Twig_Loader_Array([]);

            $debug = $this->container->get('config.debug');
            $twig = new Twig_Environment($loader, [
                'debug'            => $debug,
                'auto_reload'      => $debug,
                'strict_variables' => false,
                'cache'            => $this->container->get('paths.cache').'/twig',
            ]);

            if ($debug) {
                $twig->addExtension(new Twig_Extension_Debug());
            }

            return $twig;
        });
    }
}
