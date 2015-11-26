<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Http\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Madewithlove\Glue\Configuration\ConfigurationInterface;
use Twig_Environment;

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
            /** @var ConfigurationInterface $configuration */
            $configuration = $this->container->get(ConfigurationInterface::class);
            $configuration = $configuration->getPackageConfiguration(__CLASS__);

            $twig = new Twig_Environment($configuration['loader'], $configuration['environment']);

            foreach ($configuration['extensions'] as $extension) {
                $twig->addExtension($extension);
            }

            return $twig;
        });
    }
}
