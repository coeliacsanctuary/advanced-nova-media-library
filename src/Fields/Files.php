<?php

namespace Jpeters8889\AdvancedNovaMediaLibrary\Fields;

/**
 * Class Files
 *
 * @package Jpeters8889\AdvancedNovaMediaLibrary\Fields
 */
class Files extends Images
{
    protected $defaultValidatorRules = [];

    public $meta = ['type' => 'file'];

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->hideFromIndex();
    }
}
