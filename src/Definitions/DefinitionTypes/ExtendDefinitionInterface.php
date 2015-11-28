<?php
namespace Madewithlove\Glue\Definitions\DefinitionTypes;

use Interop\Container\Definition\ObjectDefinitionInterface;

interface ExtendDefinitionInterface extends ObjectDefinitionInterface
{
    /**
     * @return string
     */
    public function getExtended();
}
