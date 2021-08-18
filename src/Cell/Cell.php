<?php

declare(strict_types=1);

namespace Palmtree\Csv\Cell;

use Palmtree\Csv\Normalizer\NormalizerInterface;

class Cell
{
    private ?NormalizerInterface $normalizer = null;
    private ?string $value = null;

    public function __construct(string $value, NormalizerInterface $normalizer)
    {
        $this->setRawValue($value);
        $this->setNormalizer($normalizer);
    }

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
