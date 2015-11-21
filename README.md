[![Build Status](https://travis-ci.org/madewithlove/nanoframework-helpers.svg)](https://travis-ci.org/madewithlove/nanoframework-helpers)

# Madewithlove Nanoframework helpers

This is a set of helpers to quickly bootstrap a package-based application.
It's all service providers and middlewares so although the defaults leverage `league/route` and `twig/twig` per example, anything can be used with it. It's just a small time saver.

Facultative providers include:

- A base routing system using `league/route`
- A PSR7 stack using `zendframework/zend-diactoros`
- A facultative base controller and a setup `twig/twig` instance
- A database setup with `illuminate/database`
- A command bus with `league/tactician`
- Logs handling with `monolog/monolog`
- A debugbar with `maximebf/debugbar`
- A small CLi with `symfony/console`

Any of these can be overidden or removed, this package doesn't enforce any structure or the use of any dependency in particular besides `league/container` (as the Application class expects service provider capabilities).

## Usage
### Basic usage

```php
// Create an app instance with your root app path
// and simply run it
$app = new Application(__DIR__);
$app->run();
```

You can configure the application through the `configure` method which accepts an array of various parameters:

```php
$app = new Application(__DIR__);
$app->configure([
    'namespace'   => 'MyApp',
    'debug'       => getenv('APP_DEBUG'),
    'providers'   => [SomeProvider::class],
    'middlewares' => [SomeMiddleware::class],
    'paths'       => [
        'views' => __DIR__.'/paths/to/views',
    ],
]);
```

### Command line

The package also provides a small CLI, for this, same principle, create a `console` file (or whatever you want) and call the `console` method of the Application:

```php
#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

$app = new Application(realpath(__DIR__));
$app->console();
```

You can then run `php console` to access the CLI. To add commands, set the `commands` option:

```php
$app->configure([
    'commands' => [
        SomeCommand::class,
    ],
]);
```

All commands are resolved through the container so you can inject dependencies in their constructor.

### Advanced usage
For advanced usage, it is recommended to create a `Configuration` class extending `AbstractConfiguration`. You can there tweak the providers, middlewares, etc. in a more advanced fashion:

```php
namespace Acme;

class MyConfiguration extends \Madewithlove\Nanoframework\DefaultConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getProviders()
    {
        // You can override a specific provider
        // by overriding its key
        return array_merge(parent::getProviders(), [
            'routing' => MyRoutingServiceProvider::class,
            AnotherCustomProvider::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getDebugProviders()
    {
        return [SomeProvider::class];
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return $this->container->get('some.debug.value');
    }

    // etc.
}
```

And then set that class on the app:

```php
$app = new Application(__DIR__);
$app->setConfiguration(MyConfiguration::class);
$app->run();
```
