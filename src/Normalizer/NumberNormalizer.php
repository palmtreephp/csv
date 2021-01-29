<?php

namespace Palmtree\Csv\Normalizer;

/**
 * NumberNormalizer converts numeric strings to integers and floats.
 */
class NumberNormalizer extends AbstractNormalizer
{
    /** @var int|null */
    private $decimals;

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

    protected function getNormalizedValue(string $value)
    {
        if (!is_numeric($value)) {
            return 0;
        }

        $numberValue = trim($value) * 1;

        if ($this->decimals !== null) {
            $numberValue = round($numberValue, $this->decimals);
        }

        return $numberValue;
    }
}
