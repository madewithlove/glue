# Basic usage

## Creating an app

Glue is rather simple to bootstrap, simply create an `index.php` file in a directory of your choice (`/public` or `/web` per example).
Then create a new instance of `Application` with the path to your root directiry, and call `run` on it.

**public/index.php**
```php
// Create an app instance with your root app path
// and simply run it
$app = new Application(__DIR__.'/..');
$app->run();
```

## Configuration

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

While Glue doesn't assume any directory structure, here is the paths configured by default:

```
'builds'     => $rootPath.'/public/builds',
'factories'  => $rootPath.'/resources/factories',
'migrations' => $rootPath.'/resources/migrations',
'views'      => $rootPath.'/resources/views',
'cache'      => $rootPath.'/storage/cache',
'logs'       => $rootPath.'/storage/logs',
```

## Changing container

The application also implements `ContainerAwareInterface` so you can swap the container at any time:

```php
$container = new Container;
$container->share(Foobar::class, function() {
    return new Foobar;
});

$app = new Application(__DIR__, $container);

// Or
$app = new Application(__DIR__);
$app->setContainer($container);
```
