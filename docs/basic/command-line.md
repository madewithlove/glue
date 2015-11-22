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
     "paths" => "Madewithlove\Glue\Providers\PathsServiceProvider",
     "commandbus" => "Madewithlove\Glue\Providers\CommandBusServiceProvider",
     "db" => "Madewithlove\Glue\Providers\DatabaseServiceProvider",
     "filesystem" => "Madewithlove\Glue\Providers\FilesystemServiceProvider",
     "logs" => "Madewithlove\Glue\Providers\LogsServiceProvider",
     "request" => "Madewithlove\Glue\Http\Providers\RequestServiceProvider",
     "routing" => "Madewithlove\Glue\Http\Providers\RoutingServiceProvider",
     "view" => "Madewithlove\Glue\Http\Providers\TwigServiceProvider",
     "pipeline" => "Madewithlove\Glue\Http\Providers\RelayServiceProvider",
     "url" => "Madewithlove\Glue\Http\Providers\UrlGeneratorServiceProvider",
     "assets" => "Madewithlove\Glue\Http\Providers\Assets\WebpackServiceProvider",
     "console" => "Madewithlove\Glue\Console\ConsoleServiceProvider",
     "migrations" => "Madewithlove\Glue\Console\PhinxServiceProvider",
     "debugbar" => "Madewithlove\Glue\Providers\DebugbarServiceProvider",
   ]
```

## Adding commands

To add commands, set the `commands` option:

```php
$app->configure('commands', [
    SomeCommand::class,
]);
```

Glue uses `symfony/console` so created commands should be instances of `Symfony\Component\Console\Command\Command`.
All commands are resolved through the container so you can inject dependencies in their constructor.

## Using a different CLI

You can of course override the console application by overriding the `console` binding in a service provider of your doing.
The commands will still be available under the `config.commands` key in the container if your new CLI supports Symfony commands (eg. [Silly], etc).

```php
$app = new Glue();
$app->configure('providers', [
    'console' => CLImateServiceProvider::class,
]);
```

[Silly]: https://github.com/mnapoli/silly
