<?php
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
