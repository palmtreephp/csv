<?php

namespace Palmtree\Csv\Normalizer;

class ArrayNormalizer extends AbstractNormalizer
{
    /** @var string */
    private $delimiter;
    /** @var StringNormalizer */
    private $stringNormalizer;

    /**
     * @param string $delimiter
     */
    public function __construct(NormalizerInterface $normalizer = null, $delimiter = ',')
    {
        $this->setDelimiter($delimiter);

        $this->stringNormalizer = new StringNormalizer();
        $this->stringNormalizer->setTrimCharMask($this->stringNormalizer->getTrimCharMask() . $this->getDelimiter());

        parent::__construct($normalizer);
    }

    public function normalize($value)
    {
        return $this->getNormalizedValue($value);
    }

    protected function getNormalizedValue($value)
    {
        $value           = $this->stringNormalizer->normalize($value);
        $normalizedValue = \explode($this->delimiter, $value);

        foreach ($normalizedValue as &$part) {
            $part = $this->normalizer->normalize($part);
        }

        return $normalizedValue;
    }

    /**
     * @param string $delimiter
     *
     * @return self
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }
}
