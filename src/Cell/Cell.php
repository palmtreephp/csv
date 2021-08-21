<?php

declare(strict_types=1);

namespace Palmtree\Csv\Cell;

use Palmtree\Csv\Normalizer\NormalizerInterface;

class Cell
{
    private NormalizerInterface $normalizer;
    private string $value;

    public function __construct(string $value, NormalizerInterface $normalizer)
    {
        $this->value = $value;
        $this->normalizer = $normalizer;
    }

    /** @return mixed */
    public function getValue()
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
        } catch (\Exception $exception) {
            $value = '';
        }

        return $value;
    }
}
