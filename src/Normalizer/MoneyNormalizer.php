<?php

namespace Palmtree\Csv\Normalizer;

class MoneyNormalizer extends AbstractNormalizer
{
    /** @var string */
    private $moneyFormat = '%.2n';

    /**
     * Sets the format passed to money_format. Defaults to %.2n which formats the number according to the current
     * locale's national currency format rounded to 2 decimal places. e.g for en_GB: £1,234.56.
     */
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
        return \money_format($this->moneyFormat, (float)$value);
    }
}
