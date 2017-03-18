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

>>> $config->definitions
=> [
     "assets" => Madewithlove\Glue\ServiceProviders\Twig\WebpackDefinition {#12},
     "request" => Madewithlove\Glue\ServiceProviders\ZendDiactorosDefinition {#13},
     "bus" => Madewithlove\Glue\ServiceProviders\TacticianDefinition {#14},
     "pipeline" => Madewithlove\Glue\ServiceProviders\RelayDefinition {#15},
     "routing" => Madewithlove\Glue\ServiceProviders\LeagueRouteDefinition {#16},
     "db" => Madewithlove\Glue\ServiceProviders\EloquentDefinition {#17},
     "filesystem" => Madewithlove\Glue\ServiceProviders\FlysystemDefinition {#18},
     "logging" => Madewithlove\Glue\ServiceProviders\MonologDefinition {#20},
     "console" => Madewithlove\Glue\ServiceProviders\Console\SymfonyConsoleDefinition {#21},
     "views" => Madewithlove\Glue\ServiceProviders\Twig\TwigDefinition {#22},
     "url" => Madewithlove\Glue\ServiceProviders\Twig\UrlGeneratorDefinition {#25},
     "debugbar" => Madewithlove\Glue\ServiceProviders\DebugbarDefinition {#26},
     "migrations" => Madewithlove\Glue\ServiceProviders\Console\PhinxDefinition {#27},
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
