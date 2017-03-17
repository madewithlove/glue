<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\ServiceProviders\Twig;

use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Twig_Environment;

class WebpackServiceProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getServices()
    {
        return [
            Twig_Environment::class => [$this, 'withAssets'],
        ];
    }

    /**
     * @param ContainerInterface $container
     * @param callable|null      $getPrevious
     *
     * @return Twig_Environment
     */
    public function withAssets(ContainerInterface $container, callable $getPrevious = null)
    {
        /** @var Twig_Environment $twig */
        $twig = $getPrevious();
        $twig->addGlobal('assets', $this->getWebpackAssets());

        return $twig;
    }

    /**
     * Bind the path to the Webpack assets to the views.
     *
     * @return array
     */
    private function getWebpackAssets()
    {
        $assets = $this->path.'/manifest.json';
        if (!file_exists($assets)) {
            return [];
        }

        $assets = file_get_contents($assets);
        $assets = json_decode($assets, true);

        return $assets;
    }
}
