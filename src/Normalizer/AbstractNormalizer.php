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

    /** @return mixed */
    abstract protected function getNormalizedValue(string $value);

    public function normalize(string $value)
    {
        $value = $this->normalizer->normalize($value);

        return $this->getNormalizedValue($value);
    }

    public function getNormalizer(): ?NormalizerInterface
    {
        return $this->normalizer;
    }
}
