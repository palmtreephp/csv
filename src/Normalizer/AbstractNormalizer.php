<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

abstract class AbstractNormalizer implements NormalizerInterface
{
    protected NormalizerInterface $normalizer;

    public function __construct(?NormalizerInterface $normalizer = null)
    {
        $this->normalizer = $normalizer ?? new NullNormalizer();
    }

    abstract protected function getNormalizedValue(string $value): mixed;

    public function normalize(string $value): mixed
    {
        $value = $this->normalizer->normalize($value);

        if (!\is_string($value)) {
            throw new \UnexpectedValueException(\sprintf('%s must be given a normalizer that returns a string, %s returned %s.', static::class, $this->normalizer::class, get_debug_type($value)));
        }

        return $this->getNormalizedValue($value);
    }
}
