<?php

namespace Palmtree\Csv\Normalizer;

abstract class AbstractNormalizer implements NormalizerInterface
{
    /** @var NormalizerInterface */
    protected $normalizer;

    /**
     * AbstractNormalizer constructor.
     *
     * @param NormalizerInterface|null $normalizer
     */
    public function __construct(NormalizerInterface $normalizer = null)
    {
        if (!$normalizer instanceof NormalizerInterface) {
            $normalizer = new NullNormalizer();
        }

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
        $value = $this->getNormalizer()->normalize($value);

        $value = $this->getNormalizedValue($value);

        return $value;
    }

    /**
     * @param NormalizerInterface $normalizer
     *
     * @return AbstractNormalizer
     */
    public function setNormalizer(NormalizerInterface $normalizer)
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
