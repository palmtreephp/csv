<?php

namespace Palmtree\Csv\Normalizer;

class HtmlNormalizer extends AbstractNormalizer
{
    /** @var bool */
    private $encode = true;
    /** @var int */
    private $flags = ENT_QUOTES;

    public function shouldEncode(): bool
    {
        return $this->encode;
    }

    public function setEncode(bool $encode): self
    {
        $this->encode = $encode;

        return $this;
    }

    public function setFlags(int $flags): self
    {
        $this->flags = $flags;

        return $this;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    protected function getNormalizedValue(string $value): string
    {
        if ($this->shouldEncode()) {
            $value = \htmlentities($value, $this->getFlags());
        } else {
            $value = \html_entity_decode($value, $this->getFlags());
        }

        return $value;
    }
}
