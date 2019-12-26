<?php

namespace Palmtree\Csv\Normalizer;

class MoneyNormalizer extends AbstractNormalizer
{
    /** @var string */
    private $moneyFormat = '%.2n';

    public function setMoneyFormat(string $moneyFormat): self
    {
        $this->moneyFormat = $moneyFormat;

        return $this;
    }

    public function getMoneyFormat(): string
    {
        return $this->moneyFormat;
    }

    protected function getNormalizedValue(string $value): string
    {
        return \money_format($this->getMoneyFormat(), $value);
    }
}
