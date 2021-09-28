<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

/**
 * StringNormalizer formats a CSV cell as a string.
 * It will trim the string by default.
 */
class StringNormalizer extends AbstractNormalizer
{
    private bool $trim = true;
    private array $trimChars = [' ', "\t", "\n", "\r", "\0", "\x0B"];

    /**
     * Sets whether the string should be trimmed. Defaults to true.
     */
    public function trim(bool $trim): static
    {
        $this->trim = $trim;

        return $this;
    }

    /**
     * Sets the character mask passed to trim(). Defaults to the mask used by trim itself.
     */
    public function trimChars(array $trimChars): static
    {
        $this->trimChars = $trimChars;

        return $this;
    }

    public function addTrimChar(string $char): static
    {
        $this->trimChars[] = $char;

        return $this;
    }

    protected function getNormalizedValue(string $value): string
    {
        if ($this->trim) {
            $value = trim($value, implode('', $this->trimChars));
        }

        return $value;
    }
}
