<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

/**
 * NumberNormalizer converts numeric strings to integers and floats.
 */
class NumberNormalizer extends AbstractNormalizer
{
    private ?int $scale = null;

    /**
     * Sets the amount of decimal places to round to. Defaults to null which performs no rounding.
     */
    public function scale(?int $scale = null): self
    {
        $this->scale = $scale;

        return $this;
    }

    protected function getNormalizedValue(string $value): float|int
    {
        if (!is_numeric($value)) {
            return 0;
        }

        $value = trim($value);
        $value = ltrim($value, '0');

        $numberValue = json_decode($value);

        if ($this->scale !== null) {
            $numberValue = round($numberValue, $this->scale);
        }

        return $numberValue;
    }
}
