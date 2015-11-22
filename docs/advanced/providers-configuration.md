# Providers configuration

If you want to configure some of the providers that come with Glue, most of the time I recommend you write your own provider and swap ours for yours:

```php
$app = new Glue();
$app->configure('providers', [
    'view' => MyTwigProvider::class,
]);
```

If however you just want to tweak one setting here and there, the `ConfigurationInterface` defines the following methods for this:

```php
public function getPackagesConfiguration();
public function getPackageConfiguration($package);
public function setPackagesConfiguration(array $configurations = []);
public function setPackageConfiguration($package, array $configuration = []);
```

Where `$package` is the FQN of the provider you want to configure (per example `TwigServiceProvider::class`).
You can dump the settings through the `tinker` command of the provided CLI:


```bash
$ php console tinker
>>> $config->getPackagesConfiguration()
=> [
     "Madewithlove\Glue\Providers\MonologServiceProvider" => [
       "path" => "/my-app/storage/logs",
       "filename" => "2015-11-22.log",
     ],

```

If per example you want to change the filename and path used by Monolog you can like this:

```php
$app = new Glue();
$app->setPackageConfiguration(MonologServiceProvider::class, [
    'path' => $app->getPath('logs'),
    'filename' => date('Y-m-d H').'.log',
]);
```

How it works is, within the provider, the `ConfigurationInterface` instance is fetched and the configuration retrieved and used through `__CLASS__`:

```php
$configuration = $this->container->get(ConfigurationInterface::class);
$configuration = $configuration->getPackageConfiguration(__CLASS__);

$logger = new Logger('app');
$path   = $configuration['path'].DS.$configuration['filename'];
```

No black magic or anything, it's just an array that I use to build up the providers.

You can use these convenience methods in your own providers as well by following the same logic.
