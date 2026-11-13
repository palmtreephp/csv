<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

class DateTimeNormalizer extends AbstractNormalizer
{
    private string $format = 'Y-m-d';

    /**
     * Sets the format passed to DateTime::createFromFormat(). Fields not present in the format
     * are reset to zero/the Unix epoch rather than the current time.
     */
    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    protected function getNormalizedValue(string $value): ?\DateTime
    {
        $format = str_contains($this->format, '!') || str_contains($this->format, '|') ? $this->format : '!' . $this->format;

        $dateTime = \DateTime::createFromFormat($format, $value);

        $errors = \DateTime::getLastErrors();

        if ($dateTime === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            return null;
        }

        return $dateTime;
    }
}
