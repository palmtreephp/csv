<?php

namespace Palmtree\Csv\Formatter;

class NumberFormatter extends AbstractFormatter
{
    protected $decimals;

    public function __construct($formatter = null, $decimals = null)
    {
        if (! $formatter instanceof FormatterInterface) {
            $decimals = $formatter;
        }

        $this->decimals = $decimals;

        parent::__construct($formatter);
    }

    /**
     * @param int $decimals
     *
     * @return NumberFormatter
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;

        return $this;
    }

    protected function getFormattedValue($value)
    {
        $numberValue = trim($value) + 0;

        if ($this->decimals !== null) {
            $numberValue = round($numberValue, $this->decimals);
        }

        return $numberValue;
    }
}
