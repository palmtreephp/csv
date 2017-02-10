<?php

namespace Palmtree\Csv\Formatter;

class MoneyFormatter extends AbstractFormatter
{
    protected $moneyFormat;

    public function __construct($formatter = null, $format = '%.2n')
    {
        parent::__construct($formatter);

        $this->moneyFormat = $format;
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

    protected function getFormattedValue($value)
    {
        return money_format($this->moneyFormat, $value);
    }

}
