<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

/**
 * NumberNormalizer converts numeric strings to integers and floats.
 */
class NumberNormalizer extends AbstractNormalizer
{
    private ?int $decimals = null;

    /**
     * Sets the amount of decimal places to round to. Defaults to null which performs no rounding.
     */
    public function setDecimals(?int $decimals = null): self
    {
        $this->decimals = $decimals;

        return $this;
    }

    public function getDecimals(): ?int
    {
        return $this->decimals;
    }

    /**
     * @return float|int
     */
    protected function getNormalizedValue(string $value)
    {
        if (!is_numeric($value)) {
            return 0;
        }

        $value = trim($value);
        $value = ltrim($value, '0');

        $numberValue = json_decode($value);

        if ($this->decimals !== null) {
            $numberValue = round($numberValue, $this->decimals);
        }

        return $numberValue;
    }
}
