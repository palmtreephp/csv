<?php

namespace Palmtree\Csv\Normalizer;

class MoneyNormalizer extends AbstractNormalizer
{
    protected $moneyFormat;

    /**
     * MoneyNormalizer constructor.
     *
     * @param NormalizerInterface|null $normalizer
     * @param string                   $format
     */
    public function __construct($normalizer = null, $format = '%.2n')
    {
        parent::__construct($normalizer);

        $this->setMoneyFormat($format);
    }

    /**
     * @param string $moneyFormat
     *
     * @return MoneyNormalizer
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

    protected function getNormalizedValue($value)
    {
        return \money_format($this->getMoneyFormat(), $value);
    }
}
