# Routing

## Defining routes

The `Glue` class delegates calls to whatever class is bound to `router` so you can set your routes in your `index.php` file directly. Per example with `league/route`:

```php
$app = new Glue();

$app->get('users/{user}', 'UsersController::show');
$app->post('users/create', 'UsersController::store');

$app->run();
```

## Controllers

Glue also comes with a slim `AbstractController` you can (or not) use. It provides a convience `render` method which calls Twig's, and it also provides a `dispatch` method to dispatch commands to the command bus.
By default the router uses the `ParamStrategy` of `league/route`:

```php
class UsersController extends Madewithlove\Http\Controllers\AbstractController
{
    public function show($user)
    {
        return $this->render('users/show.twig', compact('user'));
    }

    public function create(ServerRequestInterface $request)
    {
        // Any of these are valid
        $user = $this->dispatch(CreateUserCommand::class, $request->getAttributes());
        $user = $this->dispatchFromRequest(CreateUser::class, $request);
        $user = $this->dispatch(new CreateUser($request->getAttribute('someValue'));

        return new RedirectResponse('users/'.$user->id);
    }
}
```

If you use another router but still want the command bus helper, it's available as a trait:

```php
class UsersController
{
    use DispatchesCommands;

    /**
     * @var CommandBus
     */
    protected $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    protected function getCommandBus()
    {
        return $this->commandBus;
    }
}
```
