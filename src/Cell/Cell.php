<?php

namespace Palmtree\Csv\Cell;

use Palmtree\Csv\Normalizer\NormalizerInterface;

class Cell
{
    /** @var NormalizerInterface $normalizer */
    private $normalizer;
    /** @var string */
    private $value;

    /**
     * @param string $value
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
        return $this->normalizer->normalize($this->getRawValue());
    }

    /**
     * @return NormalizerInterface
     */
    public function getNormalizer()
    {
        return $this->normalizer;
    }

    /**
     * @return self
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
