<?php

namespace Palmtree\Csv\Normalizer;

class ArrayNormalizer extends AbstractNormalizer
{
    protected $delimiter;

    protected $stringNormalizer;

    /**
     * ArrayNormalizer constructor.
     *
     * @param null|NormalizerInterface $normalizer
     * @param string                   $delimiter
     */
    public function __construct($normalizer = null, $delimiter = ',')
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
        $value          = $this->stringNormalizer->normalize($value);
        $normalizedValue = explode($this->getDelimiter(), $value);

        foreach ($normalizedValue as &$part) {
            $part = $this->getNormalizer()->normalize($part);
        }

        return $normalizedValue;
    }

    /**
     * @param string $delimiter
     *
     * @return ArrayNormalizer
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
