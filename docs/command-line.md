# Command line

## Usage

The package also provides a small CLI, for this, same principle, create a `console` file (or whatever you want) and call the `console` method of the Glue:

```php
#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

$app = new Glue();
$app->console();
```

You can then run `php console` to access the CLI.

## Adding commands

To add commands, set the `commands` option:

```php
$app->configure([
    'commands' => [
        SomeCommand::class,
    ],
]);
```

Glue uses `symfony/console` so created commands should be instances of `Symfony\Component\Console\Command\Command`. All commands are resolved through the container so you can inject dependencies in their constructor.

## Using a different CLI

You can of course override the console application by overriding the `console` binding in the container:

```php
$container = new Container;
$container->share('console', function() {
    return new League\CLImate\CLImate;
});

$app = new Glue();
$app->setContainer($container);
```
