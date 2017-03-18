# Command line

Glue provides a small CLI to hook into. For this, same principle, create a `console` file (or whatever you want) and call the `console` method of the Glue:

```php
#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

$app = new Glue();
$app->console();
```

You can then run `php console` to access the CLI:

```bash
$ php console
Glue version 0.1.0

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  help              Displays help for a command
  list              Lists commands
  tinker            Tinker with the application and its classes
 migrate
  migrate:create    Create a new migration
  migrate:migrate   Migrate the database
  migrate:rollback  Rollback the last or to a specific migration
  migrate:status    Show migration status
```

## Tinkering with the application

Glue includes a REPL command by default which lets you access the container and configuration and any classes you might have in the current context:

```bash
$ php console tinker
>>> ls
Variables: $app, $config

>>> $config->providers
=> [
     "request" => Madewithlove\ServiceProviders\Http\ZendDiactorosServiceProvider {#23},
     "bus" => Madewithlove\ServiceProviders\CommandBus\TacticianServiceProvider {#21},
     "pipeline" => Madewithlove\ServiceProviders\Http\RelayServiceProvider {#20},
     "routing" => Madewithlove\ServiceProviders\Http\LeagueRouteServiceProvider {#19},
     "db" => Madewithlove\ServiceProviders\Database\EloquentServiceProvider {#18},
     "factories" => Madewithlove\ServiceProviders\Database\FactoryMuffinServiceProvider {#16},
     "filesystem" => Madewithlove\ServiceProviders\Filesystem\FlysystemServiceProvider {#17},
     "logging" => Madewithlove\ServiceProviders\Development\MonologServiceProvider {#14},
     "console" => Madewithlove\Glue\ServiceProviders\Console\SymfonyConsoleServiceProvider {#13},
     "views" => Madewithlove\ServiceProviders\Templating\TwigServiceProvider {#12},
     "url" => Madewithlove\Glue\ServiceProviders\Twig\UrlGeneratorServiceProvider {#11},
     "assets" => Madewithlove\Glue\ServiceProviders\Twig\WebpackServiceProvider {#10},
     "debugbar" => Madewithlove\ServiceProviders\Development\DebugbarServiceProvider {#40},
     "migrations" => Madewithlove\Glue\ServiceProviders\Console\PhinxServiceProvider {#41},
   ]
```

## Adding commands

To add commands, pass them as constructor arguments to the `SymfonyConsoleServiceProvider`:

```php
$app->setServiceProvider('console', new SymfonyConsoleDefinition([
    SomeCommand::class,
]));
```

Glue uses `symfony/console` so created commands should be instances of `Symfony\Component\Console\Command\Command`.
All commands are resolved through the container so you can inject dependencies in their constructor.

If you want to use the service provider **with** the default commands provided by Glue, you can use the factory method `withDefaultCommands`:

```php
$app->setServiceProvider('console', SymfonyConsoleServiceProvider::withDefaultCommands([
    SomeCommand::class,
]));
```

## Using a different CLI

You can of course override the console application by overriding the `console` binding in a service provider of your doing.

```php
$app = new Glue();
$app->setServiceProvider('console', new ClimateServiceProvider([
    SomeCommand::class,
    OtherCommand::class
]);
```
