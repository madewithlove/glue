<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions\Twig;

use Interop\Container\Definition\DefinitionProviderInterface;
use Madewithlove\Glue\Definitions\DefinitionTypes\ExtendDefinition;
use Twig_Environment;

class WebpackDefinition implements DefinitionProviderInterface
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
    public function getDefinitions()
    {
        $assets = new ExtendDefinition(Twig_Environment::class);
        $assets->addMethodCall('addGlobal', 'assets', $this->getWebpackAssets());

        return [$assets];
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
