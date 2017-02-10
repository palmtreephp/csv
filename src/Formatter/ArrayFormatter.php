<?php

namespace Palmtree\Csv\Formatter;

use Palmtree\Csv\Cell\ArrayCell;

class ArrayFormatter extends StringFormatter
{
    protected $delimiter;

    public static $cellClass = ArrayCell::class;

    public function __construct($formatter = null, $delimiter = ',')
    {
        parent::__construct($formatter);

        $this->delimiter = $delimiter;
        $this->trimCharMask .= $this->delimiter;
    }

    public function format($value)
    {
        $value = parent::getFormattedValue($value);

        $value = $this->getFormattedValue($value);

        return $value;
    }

    public function getFormattedValue($value)
    {
        $formattedValue = explode($this->delimiter, $value);

        if ($this->formatter instanceof FormatterInterface) {
            foreach ($formattedValue as &$part) {
                $part = $this->formatter->format($part);
            }
        }

        return $formattedValue;
    }

    /**
     * @param string $delimiter
     *
     * @return ArrayFormatter
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }
}
