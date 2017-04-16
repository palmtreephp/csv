<?php

namespace Palmtree\Csv\Formatter;

interface FormatterInterface
{
    /**
     * @param string $value
     *
     * @return mixed
     */
    public function format($value);
}
