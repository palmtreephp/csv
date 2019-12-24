<?php

namespace Palmtree\Csv\Normalizer;

/**
 * NumberNormalizer converts numeric strings to integers and floats.
 */
class NumberNormalizer extends AbstractNormalizer
{
    /** @var */
    private $decimals;

    /**
     * @param int|null $decimals
     */
    public function __construct(NormalizerInterface $normalizer = null, $decimals = null)
    {
        $this->setDecimals($decimals);

        parent::__construct($normalizer);
    }

    /**
     * @param int|null $decimals
     *
     * @return self
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    protected function getNormalizedValue($value)
    {
        if (!\is_numeric($value)) {
            return 0;
        }

        $numberValue = \trim($value) + 0;

        if ($this->getDecimals() !== null) {
            $numberValue = \round($numberValue, $this->getDecimals());
        }

        return $numberValue;
    }
}
