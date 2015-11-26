# Glue
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

![](http://i.imgur.com/3oO0UWd.png)

## What's Glue?
Glue is an adhesive substance used for sticking objects or materials together ( ͡° ͜ʖ ͡°)

**Glue** is also an helper package made to quickly bootstrap packages-based applications.
At its core it's just a container and a quick PSR7 setup, on top of which are glued together service providers and middlewares.

This is _not_ a microframework (in the sense that it doesn't frame your work). If this is what you're looking for I recommend instead using [Silex], [Slim] or whatever you want.
On the contrary, Glue is as its name indicates just a bit of glue to tie existing packages and middlewares together. It doesn't assume much, it won't get in your way, it's just a way to tie stuff together.

### What does it look like
To be concise, Glue turns a common setup such as the following (container + router + PSR7):

```php
<?php
// Create container
$container = new Container();
$container->addServiceProvider(SomeProvider::class);
$container->addServiceProvider(AnotherProvider::class);

// Create router and routes
$router = new RouteCollection($container);
$router->get('/', 'SomeController::index');

// Create PSR7 middleware handler
$builder = new RelayBuilder();
$relay = $builder->newInstance([
    SomeMiddleware::class,
    function($request, $response, $next) use ($router) {
        $next($request, $router->dispatch($request, $response));
    }
]);

// Create PSR7 stack
$request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$response = $relay(new Request, new Response());

(new SapiEmitter())->emit($response);
```

Into this:

```php
$app = (new Glue())
    ->setProviders([
        SomeProvider::class,
        AnotherProvider::class,
        LeagueRouteServiceProvider::class
    ]);
    ->setMiddlewares([
        SomeMiddleware::class,
        LeagueRouteMiddleware::class,
    ]);

// Decorates a router of your choice
$app->get('/', 'SomeController::index');

$app->run();
```

As you can see Glue serves two purposes: eliminating recurring boilerplate in binding packages together, and providing service providers for common packages such as `league/route`.
It is configurable and flexible, it won't get in your way, it's just here to help you not type the same things over and over again.

### What's in the box
Glue provides several providers out of the box:
- **Routing**
    - Base routing system with `league/route`
    - PSR7 stack with `zendframework/zend-diactoros`
    - View engine with `twig/twig`
    - Facultative base controller

- **Business**
    - Database handling with `illuminate/database`
    - Migrations with `robmorgan/phinx`
    - Command bus with `league/tactician`

- **Development**
    - Dotenv files with `vlucas/phpdotenv`
    - Logs handling with `monolog/monolog`
    - Debugbar with `maximebf/debugbar`
    - Small CLI with `symfony/console`
    - Filesystem with `league/flysystem`
    - REPL with `psy/psysh`

Any of these can be overidden or removed; this package doesn't enforce any structure or the use of any dependency in particular.

Why? Because I do a lot of very small web applications, for myself or public ones, and I was tired of going through the same routine for the hundreth time.
Then I thought others might have the same use case and here we are.

## Install

```bash
$ composer require madewithlove/glue
```

## Usage
See the [documentation] for more informations.

## Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

```bash
$ composer test
```

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security
If you discover any security related issues, please email heroes@madewithlove.be instead of using the issue tracker.

## Credits
- [Maxime Fabre][link-author]
- [All Contributors][link-contributors]

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[documentation]: http://madewithlove.github.io/glue
[silex]: http://silex.sensiolabs.org
[slim]: http://www.slimframework.com
[ico-version]: https://img.shields.io/packagist/v/madewithlove/glue.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/madewithlove/glue/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/madewithlove/glue.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/madewithlove/glue.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/madewithlove/glue.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/madewithlove/glue
[link-travis]: https://travis-ci.org/madewithlove/glue
[link-scrutinizer]: https://scrutinizer-ci.com/g/madewithlove/glue/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/madewithlove/glue
[link-downloads]: https://packagist.org/packages/madewithlove/glue
[link-author]: https://github.com/Anahkiasen
[link-contributors]: ../../contributors
