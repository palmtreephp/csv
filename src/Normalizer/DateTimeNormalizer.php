<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

class DateTimeNormalizer extends AbstractNormalizer
{
    private string $format = 'Y-m-d';

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    protected function getNormalizedValue(string $value): ?\DateTime
    {
        return \DateTime::createFromFormat($this->format, $value) ?: null;
    }
}
