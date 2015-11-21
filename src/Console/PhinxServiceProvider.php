<?php
namespace Madewithlove\Nanoframework\Console;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Phinx\Config\Config;
use Phinx\Console\Command;
use Symfony\Component\Console\Application;

class PhinxServiceProvider extends AbstractServiceProvider
{
    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        // ...
    }

    /**
     * Boot the provider
     */
    public function boot()
    {
        /** @var Application $console */
        $console = $this->container->get(Application::class);

        $console->addCommands([
            $this->getCommand(new Command\Create()),
            $this->getCommand(new Command\Migrate()),
            $this->getCommand(new Command\Rollback()),
            $this->getCommand(new Command\Status()),
        ]);
    }

    /**
     * @param Command\AbstractCommand $command
     *
     * @return Command\AbstractCommand
     */
    protected function getCommand(Command\AbstractCommand $command)
    {
        $command->setName('migrate:'.$command->getName());
        $command->setConfig($this->getConfiguration());

        return $command;
    }

    /**
     * Get the Phinx configuration
     *
     * @return Config
     */
    private function getConfiguration()
    {
        return new Config([
            'paths'        => [
                'migrations' => $this->container->get('paths.migrations'),
            ],
            'environments' => [
                'default_migration_table' => 'phinxlog',
                'default_database'        => 'default',
                'default'                 => [
                    'adapter' => 'mysql',
                    'host'    => getenv('DB_HOST'),
                    'name'    => getenv('DB_DATABASE'),
                    'user'    => getenv('DB_USERNAME'),
                    'pass'    => getenv('DB_PASSWORD'),
                    'port'    => 3306,
                    'charset' => 'utf8',
                ],
            ],
        ]);
    }
}
