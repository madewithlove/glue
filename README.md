# Glue
[![Build Status](https://travis-ci.org/madewithlove/nanoframework-helpers.svg)](https://travis-ci.org/madewithlove/nanoframework-helpers)

Glue is a nano-framework made to quickly bootstrap packages-based applications.
At its core it's just a container and a quick PSR7 setup, on top of which are glued together service providers and middlewares. So although the defaults leverage `league/route` and `twig/twig` per example, anything can be used with it.

Default providers include:
- A base routing system using `league/route`
- A PSR7 stack using `zendframework/zend-diactoros`
- A facultative base controller and a setup `twig/twig` instance
- A database setup with `illuminate/database`
- A command bus with `league/tactician`
- Logs handling with `monolog/monolog`
- A debugbar with `maximebf/debugbar`
- A small CLi with `symfony/console`
- Migrations through `robmorgan/phinx`

Any of these can be overidden or removed; this package doesn't enforce any structure or the use of any dependency in particular besides `league/container` (as the Application class expects service provider capabilities).

## Usage
### Basic usage

**public/index.php**
```php
// Create an app instance with your root app path
// and simply run it
$app = new Application(__DIR__.'/..');
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

While Glue doesn't assume any directory structure, here is the paths configured by default:

```
'builds'     => $rootPath.'/public/builds',
'factories'  => $rootPath.'/resources/factories',
'migrations' => $rootPath.'/resources/migrations',
'views'      => $rootPath.'/resources/views',
'cache'      => $rootPath.'/storage/cache',
'logs'       => $rootPath.'/storage/logs',
```

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

### Routing
The `Application` class delegates calls to whatever class is bound to `router` so you can set your routes in your `index.php` file directly. Per example with `league/route`:

```php
$app = new Application(__DIR__.'/..');

$app->get('/users', 'UsersController::index');
$app->post('/users/create', 'UsersController::store');

$app->run();
```

Glue also comes with a slim `AbstractController` you can (or not) extend, it provides a convience `render` method which call Twig's, it also provides a `dispatch` method to dispatch commands to the command bus.
By default the router uses the ParamStrategy:

```php
class UsersController
{
    public function show($user)
    {
        return $this->render('users/show.twig', compact('user'));
    }

    public function create(ServerRequestInterface $request)
    {
        $this->dispatch(CreateUserCommand::class, $request->getAttributes());

        return new RedirectResponse('/users');
    }
}
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
