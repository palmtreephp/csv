<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

class HtmlNormalizer extends AbstractNormalizer
{
    private bool $encode = true;
    private int $flags = \ENT_QUOTES;

    /**
     * Returns whether the data will be HTML encoded.
     */
    public function shouldEncode(): bool
    {
        return $this->encode;
    }

    /**
     * Sets whether the data should HTML encoded or returned as raw HTML. Defaults to true.
     */
    public function setEncode(bool $encode): self
    {
        $this->encode = $encode;

        return $this;
    }

    /**
     * Sets the flags that are passed to htmlentities and html_entity_decode. Defaults to ENT_QUOTES.
     */
    public function setFlags(int $flags): self
    {
        $this->flags = $flags;

        return $this;
    }

    /**
     * Returns the flags that will be passed to htmlentities and html_entity_decode.
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    protected function getNormalizedValue(string $value): string
    {
        if ($this->encode) {
            $value = htmlentities($value, $this->flags);
        } else {
            $value = html_entity_decode($value, $this->flags);
        }

        return $value;
    }
}
