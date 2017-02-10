<?php

namespace Palmtree\Csv\Validator;

interface ValidatorInterface
{
    /**
     * @param mixed $value
     */
    public function setValue($value);

    public function getValue();

    /**
     * @return bool
     */
    public function validate();
}
