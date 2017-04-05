<?php

namespace Palmtree\Csv\Formatter;

abstract class AbstractFormatter implements FormatterInterface
{
    /** @var FormatterInterface|null */
    protected $formatter;

    public function __construct($formatter = null)
    {
        $this->formatter = $formatter;
    }

    abstract protected function getFormattedValue($value);

    public function format($value)
    {
        if ($this->formatter instanceof FormatterInterface) {
            $value = $this->formatter->format($value);
        }

        $value = $this->getFormattedValue($value);

        return $value;
    }
}
