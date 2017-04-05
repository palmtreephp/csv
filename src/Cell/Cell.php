<?php

namespace Palmtree\Csv\Cell;

use Palmtree\Csv\Formatter\FormatterInterface;

class Cell
{
    /** @var FormatterInterface $formatter */
    protected $formatter;
    protected $value;

    public function __construct($value, FormatterInterface $formatter = null)
    {
        $this->value     = $value;
        $this->formatter = $formatter;
    }

    public function getRawValue()
    {
        return $this->value;
    }

    public function getValue()
    {
        return $this->formatter->format($this->value);
    }

    public function getFormatter()
    {
        return $this->formatter;
    }

    public function __toString()
    {
        try {
            $value = (string)$this->getValue();
        } catch (\Exception $exception) {
            $value = '';
        }

        return $value;
    }
}
