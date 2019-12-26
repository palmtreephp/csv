<?php

namespace Palmtree\Csv\Normalizer;

abstract class AbstractNormalizer implements NormalizerInterface
{
    /** @var NormalizerInterface|null */
    protected $normalizer;

    public function __construct(NormalizerInterface $normalizer = null)
    {
        $this->setNormalizer($normalizer);
    }

    /**
     * @param string $value
     *
     * @return mixed
     */
    abstract protected function getNormalizedValue($value);

    /**
     * @inheritdoc
     */
    public function normalize($value)
    {
        if ($this->normalizer) {
            $value = $this->normalizer->normalize($value);
        }

        $value = $this->getNormalizedValue($value);

        return $value;
    }

    /**
     * @return self
     */
    public function setNormalizer(NormalizerInterface $normalizer = null)
    {
        $this->normalizer = $normalizer;

        return $this;
    }

    /**
     * @return NormalizerInterface
     */
    public function getNormalizer()
    {
        return $this->normalizer;
    }
}
