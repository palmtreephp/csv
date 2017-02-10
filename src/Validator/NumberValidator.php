<?php

namespace Palmtree\Csv\Validator;

class NumberValidator implements ValidatorInterface
{
    protected $value;
    protected $min;
    protected $max;
    protected $inclusive;

    public function __construct($value = null, $min = null, $max = null, $inclusive = true)
    {
        $this->value     = $value;
        $this->min       = $min;
        $this->max       = $max;
        $this->inclusive = $inclusive;
    }

    /**
     * @inheritDoc
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {

        $value = $this->getValue() + 0;

        if ($this->min !== null && $value <= $this->min) {
            return false;
        }

        if ($this->max !== null && $value > $this->max) {
            return false;
        }

        return true;
    }
}
