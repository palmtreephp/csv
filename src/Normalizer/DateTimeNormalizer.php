<?php

namespace Palmtree\Csv\Normalizer;

class DateTimeNormalizer extends AbstractNormalizer
{
    /** @var string */
    private $format = 'Y-m-d';

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    protected function getNormalizedValue(string $value): ?\DateTime
    {
        return \DateTime::createFromFormat($this->format, $value) ?: null;
    }
}
