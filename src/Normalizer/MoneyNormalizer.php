<?php

namespace Palmtree\Csv\Normalizer;

class MoneyNormalizer extends AbstractNormalizer
{
    /** @var string */
    private $moneyFormat;

    /**
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
     * @return self
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
