<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

/**
 * Null normalizer. Returns the value as is.
 */
class NullNormalizer implements NormalizerInterface
{
    public function normalize(string $value): string
    {
        return $value;
    }
}
