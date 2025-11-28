<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

class ArrayNormalizer extends AbstractNormalizer
{
    /** @var non-empty-string */
    private string $delimiter = ',';
    private StringNormalizer $stringNormalizer;

    public function __construct(?NormalizerInterface $normalizer = null)
    {
        $this->stringNormalizer = new StringNormalizer();
        $this->stringNormalizer->addTrimChar($this->delimiter);

        parent::__construct($normalizer);
    }

    #[\Override]
    public function normalize(string $value): array
    {
        return $this->getNormalizedValue($value);
    }

    protected function getNormalizedValue(string $value): array
    {
        $normalizedValue = explode($this->delimiter, (string)$this->stringNormalizer->normalize($value));

        foreach ($normalizedValue as &$part) {
            $part = $this->normalizer->normalize($part);
        }

        return $normalizedValue;
    }

    /**
     * Sets the delimiter to pass to explode(). Defaults to , (comma).
     *
     * @param non-empty-string $delimiter
     */
    public function delimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;

        $this->stringNormalizer = new StringNormalizer();
        $this->stringNormalizer->addTrimChar($this->delimiter);

        return $this;
    }
}
