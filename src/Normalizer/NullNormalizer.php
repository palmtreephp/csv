<?php

namespace Palmtree\Csv\Normalizer;

/**
 * Null normalizer. Returns the value as is.
 */
class NullNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function normalize(string $value): string
    {
        return $value;
    }
}
