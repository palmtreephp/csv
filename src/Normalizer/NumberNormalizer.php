<?php

namespace Palmtree\Csv\Normalizer;

/**
 * NumberNormalizer converts numeric strings to integers and floats.
 */
class NumberNormalizer extends AbstractNormalizer
{
    /** @var */
    private $decimals;

    public function setDecimals(?int $decimals): self
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
        if (!\is_numeric($value)) {
            return 0;
        }

        $numberValue = \trim($value) * 1;

        if ($this->getDecimals() !== null) {
            $numberValue = \round($numberValue, $this->getDecimals());
        }

        return $numberValue;
    }
}
