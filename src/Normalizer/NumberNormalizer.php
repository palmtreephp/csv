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

        // Only strip leading zeros if the value doesn't start with '0.' (decimal)
        // and the value is not just '0'
        if ($value !== '0' && !str_starts_with($value, '0.') && !str_starts_with($value, '-0.')) {
            $value = ltrim($value, '0');
            // If ltrim removed everything, it was just zeros - use '0'
            if ($value === '' || $value === '.') {
                $value = '0';
            }
        }

        $numberValue = json_decode($value);

        if ($numberValue === null) {
            return 0;
        }

        if ($this->scale !== null) {
            $numberValue = round($numberValue, $this->scale);
        }

        return $numberValue;
    }
}
