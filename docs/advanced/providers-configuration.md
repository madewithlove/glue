# Providers configuration

If you want to configure some of the providers that come with Glue, most of the time I recommend you write your own service provider and swap ours for yours:

```php
$app = new Glue();
$app->configure('providers', [
    'view' => new MyTwigServiceProvider(),
]);
```

If however you just want to tweak one setting here and there, most providers accepts various arguments to configure them in depth. You can look at the providers themselves to see which options they accept:

```php
class MonologDefinition implements DefinitionProviderInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $filename;
```

If per example you want to change the filename and path used by Monolog you can like this:

```php
$app = new Glue();
$app->setServiceProvider('logging', new MonologDefinition('/logs', date('m-d').'.log'));
```

You can see which provider is bound to which provider key through the `php console tinker` command:

```bash
$ php console tinker
>>> $config->providers
=> [
     "request" => Madewithlove\ServiceProviders\Http\ZendDiactorosServiceProvider {#23},
     "bus" => Madewithlove\ServiceProviders\CommandBus\TacticianServiceProvider {#21},
     "pipeline" => Madewithlove\ServiceProviders\Http\RelayServiceProvider {#20},
     "routing" => Madewithlove\ServiceProviders\Http\LeagueRouteServiceProvider {#19},
     "db" => Madewithlove\ServiceProviders\Database\EloquentServiceProvider {#18},
     "factories" => Madewithlove\ServiceProviders\Database\FactoryMuffinServiceProvider {#16},
     "filesystem" => Madewithlove\ServiceProviders\Filesystem\FlysystemServiceProvider {#17},
     "logging" => Madewithlove\ServiceProviders\Development\MonologServiceProvider {#14},
     "console" => Madewithlove\Glue\ServiceProviders\Console\SymfonyConsoleServiceProvider {#13},
     "views" => Madewithlove\ServiceProviders\Templating\TwigServiceProvider {#12},
     "url" => Madewithlove\Glue\ServiceProviders\Twig\UrlGeneratorServiceProvider {#11},
     "assets" => Madewithlove\Glue\ServiceProviders\Twig\WebpackServiceProvider {#10},
     "debugbar" => Madewithlove\ServiceProviders\Development\DebugbarServiceProvider {#40},
     "migrations" => Madewithlove\Glue\ServiceProviders\Console\PhinxServiceProvider {#41},
   ]
```

You can also see which options a provider exposes through the `php console config` command.
