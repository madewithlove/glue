<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions\DefinitionTypes;

use Assembly\ObjectDefinition;

class ExtendDefinition extends ObjectDefinition implements ExtendDefinitionInterface
{
    /**
     * @var string
     */
    protected $extended;

    /**
     * ExtendDefinition constructor.
     *
     * @param string $extended
     */
    public function __construct($extended)
    {
        $this->extended = $extended;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtended()
    {
        return $this->extended;
    }
}
