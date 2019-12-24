<?php

namespace Palmtree\Csv\Normalizer;

class DateTimeNormalizer extends AbstractNormalizer
{
    /** @var string */
    private $format;

    /**
     * @param string $format
     */
    public function __construct(NormalizerInterface $normalizer = null, $format = 'Y-m-d')
    {
        parent::__construct($normalizer);

        $this->setFormat($format);
    }

    /**
     * @param string $format
     *
     * @return self
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    protected function getNormalizedValue($value)
    {
        return \DateTime::createFromFormat($this->getFormat(), $value);
    }
}
