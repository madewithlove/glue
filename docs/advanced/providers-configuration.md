# Providers configuration

If you want to configure some of the providers that come with Glue, most of the time I recommend you write your own definition and swap ours for yours:

```php
$app = new Glue();
$app->configure('providers', [
    'view' => new MyTwigDefinition(),
]);
```

If however you just want to tweak one setting here and there, most definitions accepts various arguments to configure them in depth. You can look at the definitions themselves to see which options they accept:

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
$app->setDefinitionProvider('logging', new MonologDefinition('/logs', date('m-d').'.log'));
```

You can see which definition is bound to which provider key through the `php console tinker` command:

```bash
$ php console tinker
Psy Shell v0.6.1 (PHP 5.6.16 — cli) by Justin Hileman
>>> $config->definitions;
=> [
     "assets" => Madewithlove\Glue\Definitions\Twig\WebpackDefinition {#12},
     "request" => Madewithlove\Glue\Definitions\ZendDiactorosDefinition {#13},
     "bus" => Madewithlove\Glue\Definitions\TacticianDefinition {#14},
     "pipeline" => Madewithlove\Glue\Definitions\RelayDefinition {#15},
     "routing" => Madewithlove\Glue\Definitions\LeagueRouteDefinition {#16},
     "db" => Madewithlove\Glue\Definitions\EloquentDefinition {#17},
     "filesystem" => Madewithlove\Glue\Definitions\FlysystemDefinition {#18},
     "logging" => Madewithlove\Glue\Definitions\MonologDefinition {#20},
     "console" => Madewithlove\Glue\Definitions\Console\SymfonyConsoleDefinition {#21},
     "views" => Madewithlove\Glue\Definitions\Twig\TwigDefinition {#22},
     "url" => Madewithlove\Glue\Definitions\Twig\UrlGeneratorDefinition {#25},
     "debugbar" => Madewithlove\Glue\Definitions\DebugbarDefinition {#26},
     "migrations" => Madewithlove\Glue\Definitions\Console\PhinxDefinition {#27},
   ]
```

You can also see which options a definition exposes through the `php console config` command.
