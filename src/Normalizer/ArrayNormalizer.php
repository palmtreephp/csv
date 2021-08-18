<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

class ArrayNormalizer extends AbstractNormalizer
{
    private ?string $delimiter = null;
    private ?StringNormalizer $stringNormalizer = null;

    public function __construct(?NormalizerInterface $normalizer = null)
    {
        $this->setDelimiter(',');

        parent::__construct($normalizer);
    }

    public function normalize(string $value)
    {
        return $this->getNormalizedValue($value);
    }

    protected function getNormalizedValue(string $value)
    {
        $normalizedValue = explode($this->delimiter, $this->stringNormalizer->normalize($value)) ?: [];

        if ($this->normalizer) {
            foreach ($normalizedValue as &$part) {
                $part = $this->normalizer->normalize($part);
            }
        }

        return $normalizedValue;
    }

    /**
     * Sets the delimiter to pass to explode(). Defaults to , (comma).
     */
    public function setDelimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;

        $this->stringNormalizer = new StringNormalizer();
        $this->stringNormalizer->addTrimChar($this->delimiter);

        return $this;
    }

    public function getDelimiter(): string
    {
        return $this->delimiter;
    }
}
