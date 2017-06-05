<?php

namespace Palmtree\Csv\Normalizer;

/**
 * NumberNormalizer converts numeric strings to integers and floats.
 */
class NumberNormalizer extends AbstractNormalizer
{
    protected $decimals;

    /**
     * NumberNormalizer constructor.
     *
     * @param null|NormalizerInterface $normalizer
     * @param null|int                 $decimals
     */
    public function __construct($normalizer = null, $decimals = null)
    {
        $this->setDecimals($decimals);

        parent::__construct($normalizer);
    }

    /**
     * @param null|int $decimals
     *
     * @return NumberNormalizer
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    protected function getNormalizedValue($value)
    {
        if (!is_numeric($value)) {
            return 0;
        }

        $numberValue = trim($value) + 0;

        if ($this->getDecimals() !== null) {
            $numberValue = round($numberValue, $this->getDecimals());
        }

        return $numberValue;
    }
}
