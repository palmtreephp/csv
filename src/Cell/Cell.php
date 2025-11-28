<?php

declare(strict_types=1);

namespace Palmtree\Csv\Cell;

use Palmtree\Csv\Normalizer\NormalizerInterface;

class Cell implements \Stringable
{
    public function __construct(private readonly string $value, private readonly NormalizerInterface $normalizer)
    {
    }

    public function getValue(): mixed
    {
        return $this->normalizer->normalize($this->getRawValue());
    }

    public function getRawValue(): string
    {
        return $this->value;
    }

    public function getNormalizer(): NormalizerInterface
    {
        return $this->normalizer;
    }

    public function __toString(): string
    {
        try {
            $value = (string)$this->getValue();
        } catch (\Exception) {
            $value = '';
        }

        return $value;
    }
}
