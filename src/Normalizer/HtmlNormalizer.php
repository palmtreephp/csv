<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

class HtmlNormalizer extends AbstractNormalizer
{
    private bool $encode = true;
    private int $flags = \ENT_QUOTES;

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

    protected function getNormalizedValue(string $value): string
    {
        return $this->encode ? htmlentities($value, $this->flags) : html_entity_decode($value, $this->flags);
    }
}
