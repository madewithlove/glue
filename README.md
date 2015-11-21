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

Any of these can be overidden, this package doesn't enforce any structure or the use of any dependency in particular besides `league/container` (as the Application class expects service provider capabilities).

## Usage
### Base usage

```php
// Create an app instance with your root app path
// and simply ru nit
$app = new Application(__DIR__);
$app->run();
```

### Advanced usage
For advanced usage, it is recommended to create a `Configuration` class extending `DefaultConfiguration`. You can there tweak the providers, the paths, the middlewares, etc:

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
        return getenv('APP_DEBUG');
    }
}
```

And then set that class on the app:

```php
$app = new Application(__DIR__);
$app->setConfiguration(MyConfiguration::class);
$app->run();
```
