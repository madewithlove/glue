<?php

namespace Madewithlove\Nanoframework\Http\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Madewithlove\Nanoframework\Services\UrlGenerator;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;

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
            $loader = new Twig_Loader_Filesystem($this->container->get('paths.views'));
            $debug = $this->container->get('config.debug');
            $twig = new Twig_Environment($loader, [
                'debug'            => $debug,
                'auto_reload'      => $debug,
                'strict_variables' => false,
                'cache'            => $this->container->get('paths.cache').'/twig',
            ]);

            // Configure Twig
            $this->registerGlobalVariables($twig);
            $this->addTwigExtensions($twig);

            return $twig;
        });
    }

    /**
     * Add extensions to Twig.
     *
     * @param Twig_Environment $twig
     */
    private function addTwigExtensions(Twig_Environment $twig)
    {
        $twig->addExtension(new Twig_Extension_Debug());

        $twig->addFunction(new Twig_SimpleFunction('url', function ($action, $parameters = []) {
            return $this->container->get(UrlGenerator::class)->to($action, $parameters);
        }));
    }

    /**
     * Register global variables with Twig.
     *
     * @param Twig_Environment $twig
     */
    private function registerGlobalVariables(Twig_Environment $twig)
    {
        $twig->addGlobal('assets', $this->getWebpackAssets());
    }

    /**
     * Bind the path to the Webpack assets to the views.
     *
     * @return array
     */
    private function getWebpackAssets()
    {
        $assets = $this->container->get('paths.builds').'/manifest.json';
        if (!file_exists($assets)) {
            return [];
        }

        $assets = file_get_contents($assets);
        $assets = json_decode($assets, true);

        return $assets;
    }
}
