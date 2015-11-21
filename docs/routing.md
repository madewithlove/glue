# Routing

## Defining routes

The `Glue` class delegates calls to whatever class is bound to `router` so you can set your routes in your `index.php` file directly. Per example with `league/route`:

```php
$app = new Glue();

$app->get('users', 'UsersController::index');
$app->post('users/create', 'UsersController::store');

$app->run();
```

## Controllers

Glue also comes with a slim `AbstractController` you can (or not) use. It provides a convience `render` method which call Twig's, and it also provides a `dispatch` method to dispatch commands to the command bus.
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
        $user = $this->dispatch(CreateUserCommand::class, $request->getAttributes());

        return new RedirectResponse('users/'.$user->id);
    }
}
```
