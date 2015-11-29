<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions\DefinitionTypes;

use Interop\Container\Definition\ObjectDefinitionInterface;

interface ExtendDefinitionInterface extends ObjectDefinitionInterface
{
    /**
     * @return string
     */
    public function getExtended();
}
