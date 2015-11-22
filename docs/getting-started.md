# Basic usage

## Getting started

### In an empty folder

Glue is rather simple to setup, it includes a small bootstrapping utility to help you set things up.
Start by requiring glue to your application:

```bash
$ composer init
$ composer require madewithlove/glue
```

Then run the following command:

```bash
$ vendor/bin/glue bootstrap
```

### In an existing folder structure

If you already have an existing structure, simply create a web-facing file (`public/index.php` or `web/index.php` or whatever). Create a new Glue application in it and call `run` on it:

**public/index.php**
```php
require '../vendor/autoload.php';

$app = new Madewithlove\Glue\Glue();
$app->run();
```

## Configuration

You configure the application by passing a `ConfigurationInterface` implementation to the constructor.
If none is passed, Glue will use the `DefaultConfiguration` class which provides some functionnality out of the box.

```php
$app = new Glue(new Configuration([
    'namespace'   => 'Acme',
    'debug'       => getenv('APP_DEBUG'),
    'providers'   => [
        Madewithlove\Glue\Http\Providers\RoutingServiceProvider::class,
        Acme\My\Own\Provider::class
    ],
    'middlewares' => [
        Madewithlove\Glue\Http\Middlewares\LeagueRouterMiddleware::class,
        Psr7Middlewares\Middleware\FormatNegotiator::class,
    ],
    'paths'       => [
        'views' => __DIR__.'/paths/to/views',
    ],
]));
```

You can also override certain parts of the configuration through the `configure` method:

```php
// This will override the `namespace` config value in `MyConfiguration`
$app = new Glue(new MyConfiguration());

$app->configure(['namespace' => 'MyApp']); // or
$app->configure('namespace', 'MyApp');
```

This method uses a recursive merge strategy so you can override specific providers from the `DefaultConfiguration` this way:

```php
$app->configure([
    'providers' => [
        'view' => MyPlatesServiceProvider::class,
    ],
]);
```

Any configuration key passed to Glue will be bound on the container as `config.{key}`, per example if you need to share a configuration
value amongst your application, simply pass it to the configuration:

```php
// Will make `$this->container->get('config.my_key')` available in providers and such
$app->configure('my_key', 'some_value');
```

Ultimately, the configuration is freeform and besides two keys (`debug` and `paths`) none of the values are _really_ required.
You can create your configuration however you'd like in whatever format you'd like. Make it a JSON, or a YAML file, use a third-party package, whatever you want.
Ultimately you just have to pass a `ConfigurationInterface` to Glue, with the base `Configuration` class accepting an array.

## Environment variables

Some configuration, like database credentials and such, are fetched through environment variables.
By default Glue will attempt to load an `.env` file in the root path if found, so you can define things there too.

## Directory structure

While Glue doesn't assume any directory structure, here are the paths configured by the `DefaultConfiguration`:

```
'assets'     => 'public/builds',
'web'        => 'public',
'migrations' => 'resources/migrations',
'views'      => 'resources/views',
'cache'      => 'storage/cache',
'logs'       => 'storage/logs',
```

Those are of course only needed in case you use the related providers, if per example you don't use migrations, you won't need
the `migrations` path, and so on. You can quickly generate the folders **according to the passed configuration** by running the following command:

```bash
$ php console glue:bootstrap
✓ Created public/builds
✓ Created resources/factories
✓ Created resources/migrations
✓ Created resources/views
✓ Created storage/cache
✓ Created storage/logs
✓ Created .env
✓ Created public/index.php
✓ Created console
```

## Changing container

The application also implements `ContainerAwareInterface` so you can swap the container at any time:

```php
$container = new Container;
$container->share(Foobar::class, function() {
    return new Foobar;
});

$app = new Glue();
$app->setContainer($container);
```

It has however to be an instance of `League\Container` as Glue relies heavily on its service provider feature.
