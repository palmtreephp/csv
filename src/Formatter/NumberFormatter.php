<?php

namespace Palmtree\Csv\Formatter;

/**
 * NumberFormatter converts numeric strings to ints and floats.
 */
class NumberFormatter extends AbstractFormatter
{
    protected $decimals;

    /**
     * NumberFormatter constructor.
     *
     * @param null|FormatterInterface $formatter
     * @param null|int                $decimals
     */
    public function __construct($formatter = null, $decimals = null)
    {
        $this->setDecimals($decimals);

        parent::__construct($formatter);
    }

    /**
     * @param null|int $decimals
     *
     * @return NumberFormatter
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    protected function getFormattedValue($value)
    {
        $numberValue = is_numeric($value) ? trim($value) + 0 : 0;

        if ($this->getDecimals() !== null) {
            $numberValue = round($numberValue, $this->getDecimals());
        }

        return $numberValue;
    }
}
