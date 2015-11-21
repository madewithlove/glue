<?php
namespace Madewithlove\Glue\Configuration;

class ArrayConfiguration extends AbstractConfiguration
{
    /**
     * @param array $configuration
     */
    public function __construct(array $configuration = [])
    {
        foreach ($configuration as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
