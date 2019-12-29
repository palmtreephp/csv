<?php

namespace Palmtree\Csv\Normalizer;

class ArrayNormalizer extends AbstractNormalizer
{
    /** @var string */
    private $delimiter = ',';
    /** @var StringNormalizer */
    private $stringNormalizer;

    public function __construct(NormalizerInterface $normalizer = null)
    {
        $this->stringNormalizer = new StringNormalizer();
        $this->stringNormalizer->setTrimCharMask($this->stringNormalizer->getTrimCharMask() . $this->delimiter);

        parent::__construct($normalizer);
    }

    public function normalize(string $value)
    {
        return $this->getNormalizedValue($value);
    }

    protected function getNormalizedValue(string $value)
    {
        $value           = $this->stringNormalizer->normalize($value);
        $normalizedValue = \explode($this->delimiter, $value) ?: [];

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

        $this->stringNormalizer->setTrimCharMask($this->stringNormalizer->getTrimCharMask() . $this->delimiter);

        return $this;
    }

    public function getDelimiter(): string
    {
        return $this->delimiter;
    }
}
