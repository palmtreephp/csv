<?php

namespace Palmtree\Csv\Cell;

use Palmtree\Csv\Normalizer\NormalizerInterface;

class Cell
{
    /** @var NormalizerInterface $normalizer */
    private $normalizer;
    /** @var string */
    private $value;

    public function __construct(string $value, NormalizerInterface $normalizer)
    {
        $this->setRawValue($value);
        $this->setNormalizer($normalizer);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->normalizer->normalize($this->getRawValue());
    }

    public function setRawValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getRawValue(): string
    {
        return $this->value;
    }

    public function getNormalizer(): NormalizerInterface
    {
        return $this->normalizer;
    }

    public function setNormalizer(NormalizerInterface $normalizer): self
    {
        $this->normalizer = $normalizer;

        return $this;
    }

    public function __toString(): string
    {
        try {
            $value = (string)$this->getValue();
        } catch (\Exception $exception) {
            $value = '';
        }

        return $value;
    }
}
