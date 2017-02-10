<?php

namespace Palmtree\Csv\Validator;

class NotBlankValidator implements ValidatorInterface
{
    protected $value;

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function validate()
    {
        return $this->getValue() !== false
               && $this->getValue() !== null
               && (string)$this->getValue() !== '';
    }

}
