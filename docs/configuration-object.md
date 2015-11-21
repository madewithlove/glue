# The Configuration object

For advanced usage, it is recommended to create a `Configuration` class extending `AbstractConfiguration`.
You can there tweak the providers, middlewares, etc. in a more advanced fashion within the `configure` method.
That method is called once the container is set on the configuration and Dotenv files are loaded etc.

```php
namespace Acme;

class MyConfiguration extends \Madewithlove\Glue\AbstractConfiguration
{
    protected function configure()
    {
        $this->debug = $this->container->get('something') ?: getenv('FOOBAR');
        $this->providers = [SomeProvider::class];
    }
}
```

And then pass that class on the app:

```php
$app = new Glue(new MyConfiguration());
$app->run();
```
