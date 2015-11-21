<?php

namespace Madewithlove\Glue\Providers;

use Illuminate\Database\Capsule\Manager;
use League\Container\ServiceProvider\AbstractServiceProvider;

class DatabaseServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        Manager::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $this->container->share(Manager::class, function () {
            $capsule = new Manager();
            $capsule->addConnection([
                'driver'    => 'mysql',
                'host'      => getenv('DB_HOST'),
                'database'  => getenv('DB_DATABASE'),
                'username'  => getenv('DB_USERNAME'),
                'password'  => getenv('DB_PASSWORD'),
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
            ]);

            // Configure database capsule
            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            return $capsule;
        });
    }

    /**
     * Boot the provider.
     */
    public function boot()
    {
        $database = $this->container->get(Manager::class);
    }
}
