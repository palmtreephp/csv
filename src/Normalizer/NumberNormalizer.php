<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

/**
 * NumberNormalizer converts numeric strings to integers and floats.
 */
class NumberNormalizer extends AbstractNormalizer
{
    private ?int $scale = null;
    private bool $nullable = false;

    /**
     * Sets the amount of decimal places to round to. Defaults to null which performs no rounding.
     */
    public function scale(?int $scale = null): self
    {
        $this->scale = $scale;

        return $this;
    }

    /**
     * Sets whether non-numeric values, including empty cells, return null. Defaults to false,
     * meaning they return 0 and can't be told apart from a real zero.
     */
    public function nullable(bool $nullable): self
    {
        $this->nullable = $nullable;

        return $this;
    }

    protected function getNormalizedValue(string $value): float|int|null
    {
        if (!is_numeric($value)) {
            return $this->nullable ? null : 0;
        }

        $numberValue = +$value;

        if ($this->scale !== null) {
            $numberValue = round($numberValue, $this->scale);
        }

        return $numberValue;
    }
}
