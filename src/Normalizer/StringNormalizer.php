<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

/**
 * StringNormalizer formats a CSV cell as a string.
 * It will trim the string by default.
 */
class StringNormalizer extends AbstractNormalizer
{
    /** @var bool */
    private $trim = true;
    /** @var array */
    private $trimChars = [' ', "\t", "\n", "\r", "\0", "\x0B"];

    /**
     * Sets whether the string should be trimmed. Defaults to true.
     */
    public function setTrim(bool $trim): self
    {
        $this->trim = $trim;

        return $this;
    }

    /**
     * Sets the character mask passed to trim(). Defaults to the mask used by trim itself.
     */
    public function setTrimChars(array $trimChars): self
    {
        $this->trimChars = $trimChars;

        return $this;
    }

    public function addTrimChar(string $char): self
    {
        $this->trimChars[] = $char;

        return $this;
    }

    public function getTrimChars(): array
    {
        return $this->trimChars;
    }

    public function shouldTrim(): bool
    {
        return $this->trim;
    }

    protected function getNormalizedValue(string $value): string
    {
        if ($this->trim) {
            $value = trim($value, implode('', $this->trimChars));
        }

        return $value;
    }
}
