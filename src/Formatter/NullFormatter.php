<?php

namespace Palmtree\Csv\Formatter;

/**
 * Null formatter. Returns the value as is.
 */
class NullFormatter implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public function format($value)
    {
        return $value;
    }
}
