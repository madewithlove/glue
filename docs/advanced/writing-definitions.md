# Writing definitions

Writing definitions can be daunting but it's a lot easier than you might think. First of all you need to create a class implementing the `DefinitionProviderInterface`. It only needs to have one method called `getDefinitions` that returns an array of definitions:

```php
class MyProvider implements DefinitionProviderInterface
{
    public function getDefinitions()
    {
        return [
            // Your definitions go here
        ];
    }
}
```

This array is an associative array where the value is what will be bound on the container, and the key what it will be bound as.

Per example using the most basic definition, the `ParameterDefinition` which is just a wrapper for "any value", this provider:

```php
class MyProvider implements DefinitionProviderInterface
{
    public function getDefinitions()
    {
        return [
            'foo' => new ParameterDefinition('bar'),
        ];
    }
}
```

Would bind the string `bar` under the key `foo` in the container.

## Objects

The most common type of things you'll want to define are objects: services, instances, etc.
There are two classes you'll want to use for this: the `ObjectDefinition` and the `FactoryCallDefinition`.

The former just defines how to create an object, you say what class you want to create, its arguments, and possible methods to call on it:

```php
$object = new ObjectDefinition(SomeClass::class);
$object->setConstructorArguments('foo', 'bar');
$object->addMethodCall('setLogger', new Logger());

// You can also use the second argument of ObjectDefinition to pass constructor arguments
$object = new ObjectDefinition(SomeClass::class, ['foo', 'bar']);
```

If any of these arguments need to be something already in the container, you can use the `Reference` class for this, which will retrieve the object from the container before using it.
Per example if we wanted to call the `setLogger` method of our object with whatever is bound to `LoggerInterface` on the container, we'd do this:

```php
$object = new ObjectDefinition(SomeClass::class);
$object->setConstructorArguments('foo', 'bar');
$object->addMethodCall('setLogger', new Reference(LoggerInterface::class));
```

The `FactoryCallDefinition` works differently in the sense that you call a callable that will return your instance, already prepared:

```php
$object = new FactoryCallDefinition(MyFactory::class, 'theMethodToCall', ['argument1', 'argument2']);

// Or use setArguments
$object = new FactoryCallDefinition(MyFactory::class, 'theMethodToCall');
$object->setArguments('argument1', 'argument2');
```

Per example with `zend/diactoros` to create a request this is what is done in Glue:

```php
$request = new FactoryCallDefinition(ServerRequestFactory::class, 'fromGlobals');
```

Once you have your object, you simply return it in the array of your provider, its key being what it'll be bound as:

```php
class MyProvider implements DefinitionProviderInterface
{
    public function getDefinitions()
    {
        $object = new ObjectDefinition(SomeClass::class);
        $object->setConstructorArguments('foo', 'bar');
        $object->addMethodCall('setLogger', new Reference(LoggerInterface::class));

        return [
            SomeClass::class => $object,
        ];
    }
}
```

## Aliases

You might also want to bind aliases on the container, for this you simply use `Reference` again and return it directly as a definition:

```php
class MyProvider implements DefinitionProviderInterface
{
    public function getDefinitions()
    {
        $object = new ObjectDefinition(SomeClass::class);
        $object->setConstructorArguments('foo', 'bar');
        $object->addMethodCall('setLogger', new Reference(LoggerInterface::class));

        return [
            SomeClass::class => $object,
            'alias-to-some-class' => new Reference(SomeClass::class),
        ];
    }
}
```
