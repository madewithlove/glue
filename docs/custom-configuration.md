# Custom configuration

For advanced usage, it is recommended to create a `Configuration` class extending `AbstractConfiguration`. You can there tweak the providers, middlewares, etc. in a more advanced fashion:

```php
namespace Acme;

class MyConfiguration extends \Madewithlove\Glue\DefaultConfiguration
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
$app = new Glue();
$app->setConfiguration(new MyConfiguration());
$app->run();
```
