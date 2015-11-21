<?php

namespace Madewithlove\Glue\Configuration;

interface ConfigurationInterface
{
    /**
     */
    public function configure();

    /**
     * @return array
     */
    public function toArray();
}
