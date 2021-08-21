<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

abstract class AbstractNormalizer implements NormalizerInterface
{
    protected ?NormalizerInterface $normalizer = null;

    public function __construct(?NormalizerInterface $normalizer = null)
    {
        $this->setNormalizer($normalizer);
    }

    /**
     * Alternative method of instantiation for chaining.
     *
     * @psalm-suppress UnsafeInstantiation
     *
     * @return static
     */
    public static function create(?NormalizerInterface $normalizer = null)
    {
        return new static($normalizer);
    }

    /** @return mixed */
    abstract protected function getNormalizedValue(string $value);

    public function normalize(string $value)
    {
        if ($this->normalizer) {
            $value = $this->normalizer->normalize($value);
        }

        return $this->getNormalizedValue($value);
    }

    public function setNormalizer(?NormalizerInterface $normalizer = null): self
    {
        $this->normalizer = $normalizer;

        return $this;
    }

    public function getNormalizer(): ?NormalizerInterface
    {
        return $this->normalizer;
    }
}
