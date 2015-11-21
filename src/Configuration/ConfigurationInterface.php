<?php

namespace Madewithlove\Glue\Configuration;

interface ConfigurationInterface
{
    /**
     * @return string
     */
    public function getNamespace();

    /**
     * @return string
     */
    public function getRootPath();

    /**
     * @return boolean
     */
    public function isDebug();

    /**
     * @return string[]
     */
    public function getProviders();

    /**
     * @return string[]
     */
    public function getMiddlewares();

    /**
     * @return string[]
     */
    public function getCommands();

    /**
     * @return string[]
     */
    public function getPaths();

    /**
     * @return array
     */
    public function toArray();
}
