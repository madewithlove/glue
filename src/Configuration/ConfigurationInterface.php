<?php

namespace Madewithlove\Glue\Configuration;

interface ConfigurationInterface
{
    /**
     * @return array
     */
    public function getConfiguration();

    /**
     * @return string[]
     */
    public function getProviders();

    /**
     * @return string[]
     */
    public function getDebugProviders();

    /**
     * @return string[]
     */
    public function getPaths();

    /**
     * @return string[]
     */
    public function getMiddlewares();

    /**
     * @return string[]
     */
    public function getConsoleCommands();

    /**
     * @return bool
     */
    public function isDebug();
}
