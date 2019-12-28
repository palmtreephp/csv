<?php

namespace Palmtree\Csv\Normalizer;

/**
 * StringNormalizer formats a CSV cell as a string.
 * It will trim the string by default.
 */
class StringNormalizer extends AbstractNormalizer
{
    /** @var bool */
    private $trim = true;
    /** @var string */
    private $trimCharMask = " \t\n\r\0\x0B";

    public function setTrim(bool $trim): self
    {
        $this->trim = $trim;

        return $this;
    }

    public function setTrimCharMask(string $trimCharMask): self
    {
        $this->trimCharMask = $trimCharMask;

        return $this;
    }

    public function getTrimCharMask(): string
    {
        return $this->trimCharMask;
    }

    public function shouldTrim(): bool
    {
        return $this->trim;
    }

    protected function getNormalizedValue(string $value): string
    {
        if ($this->trim) {
            $value = \trim($value, $this->getTrimCharMask());
        }

        return $value;
    }
}
