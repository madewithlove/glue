# Swapping middlewares handler

Most of the things that come with Glue are easily swappable as they're simply service providers you can or not include.
The PSR7 middlewares handlers ([Relay] by default) is however more ingrained into Glue, you can still easily change it though by changing the `pipeline` binding on the container.

It simply has to return a callable accepting a request and response, and returning a response.
So it can be a closure, or any class with an `__invoke` magic method:

```php
// With a closure
$container = new Container();
$container->share('pipeline', function() {
    return function ($request, $response) {
        // Process my request and response here
    };
});

// With a class implementing __invoke
$container = new Container();
$container->share('pipeline', function() {
    $pipe = new MyMiddlewareHandler();

    $middlewares = $this->container->get('config.middlewares');
    foreach ($middlewares as $middleware) {
        $pipe->pipe($middleware);
    }

    return $pipe;
});

$app = new Glue();
$app->setContainer($container);
```

[Relay]: http://relayphp.com/
