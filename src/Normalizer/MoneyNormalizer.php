<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

class MoneyNormalizer extends AbstractNormalizer
{
    private \NumberFormatter $formatter;
    private string $locale = 'en_GB';
    private string $currency = 'GBP';

    public function __construct(?NormalizerInterface $normalizer = null)
    {
        if (!class_exists('NumberFormatter')) {
            throw new \LogicException('NumberFormatter class does not exist. Is the PHP intl extension installed?');
        }

        $this->createFormatter();

        parent::__construct($normalizer);
    }

    public function locale(string $locale): static
    {
        $this->locale = $locale;

        $this->createFormatter();

        return $this;
    }

    public function currency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    protected function getNormalizedValue(string $value): string
    {
        return $this->formatter->formatCurrency((float)$value, $this->currency);
    }

    private function createFormatter(): void
    {
        $this->formatter = new \NumberFormatter($this->locale, \NumberFormatter::CURRENCY);
    }
}
