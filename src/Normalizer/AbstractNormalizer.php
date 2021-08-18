<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

abstract class AbstractNormalizer implements NormalizerInterface
{
    /** @var NormalizerInterface|null */
    protected $normalizer;

    public function __construct(?NormalizerInterface $normalizer = null)
    {
        $this->setNormalizer($normalizer);
    }

    /**
     * Alternative method of instantiation for chaining.
     *
     * @param NormalizerInterface|null $normalizer
     *
     * @return static
     */
    public static function create($normalizer = null)
    {
        return new static(...\func_get_args());
    }

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
