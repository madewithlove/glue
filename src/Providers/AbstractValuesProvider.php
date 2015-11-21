<?php

namespace Madewithlove\Nanoframework\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;

abstract class AbstractValuesProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $key;

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $values = $this->getValues();
        foreach ($values as $key => $value) {
            $this->container->add($this->key.'.'.$key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function provides($alias = null)
    {
        $provided = array_map(function ($key) {
            return $this->key.'.'.$key;
        }, $this->getProvided());

        if (!is_null($alias)) {
            return (in_array($alias, $provided, true));
        }

        return $provided;
    }

    /**
     * @return array
     */
    protected function getProvided()
    {
        return array_keys($this->getValues());
    }

    /**
     * @return array
     */
    abstract protected function getValues();
}
