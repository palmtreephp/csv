<?php

namespace Palmtree\Csv\Formatter;

class MoneyFormatter extends AbstractFormatter
{
    protected $moneyFormat;

    /**
     * MoneyFormatter constructor.
     *
     * @param null|FormatterInterface $formatter
     * @param string                  $format
     */
    public function __construct($formatter = null, $format = '%.2n')
    {
        parent::__construct($formatter);

        $this->setMoneyFormat($format);
    }

    /**
     * @param string $moneyFormat
     *
     * @return MoneyFormatter
     */
    public function setMoneyFormat($moneyFormat)
    {
        $this->moneyFormat = $moneyFormat;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMoneyFormat()
    {
        return $this->moneyFormat;
    }

    protected function getFormattedValue($value)
    {
        return money_format($this->getMoneyFormat(), $value);
    }
}
