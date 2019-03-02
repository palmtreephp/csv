<?php

namespace Palmtree\Csv\Cell;

use Palmtree\Csv\Normalizer\NormalizerInterface;

class Cell
{
    /** @var NormalizerInterface $normalizer */
    protected $normalizer;
    /** @var string */
    protected $value;

    /**
     * Cell constructor.
     *
     * @param string              $value
     * @param NormalizerInterface $normalizer
     */
    public function __construct($value, NormalizerInterface $normalizer)
    {
        $this->setRawValue($value);
        $this->setNormalizer($normalizer);
    }

    /**
     * @return string
     */
    public function getRawValue()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->getNormalizer()->normalize($this->getRawValue());
    }

    /**
     * @return NormalizerInterface
     */
    public function getNormalizer()
    {
        return $this->normalizer;
    }

    /**
     * @param NormalizerInterface $normalizer
     *
     * @return Cell
     */
    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;

        return $this;
    }

    public function __toString()
    {
        try {
            $value = (string)$this->getValue();
        } catch (\Exception $exception) {
            $value = '';
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return Cell
     */
    public function setRawValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
