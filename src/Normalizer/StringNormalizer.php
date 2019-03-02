<?php

namespace Palmtree\Csv\Normalizer;

/**
 * StringNormalizer formats a CSV cell as a string.
 * It will trim the string by default.
 */
class StringNormalizer extends AbstractNormalizer
{
    /** @var bool */
    private $trim;
    /** @var string */
    private $trimCharMask;

    /**
     * StringNormalizer constructor.
     *
     * @param NormalizerInterface|null $normalizer
     * @param bool                     $trim
     * @param string                   $trimCharMask
     */
    public function __construct(NormalizerInterface $normalizer = null, $trim = true, $trimCharMask = " \t\n\r\0\x0B")
    {
        parent::__construct($normalizer);

        $this->setTrim($trim)
             ->setTrimCharMask($trimCharMask);
    }

    /**
     * @param bool $trim
     *
     * @return StringNormalizer
     */
    public function setTrim($trim)
    {
        $this->trim = (bool)$trim;

        return $this;
    }

    /**
     * @param string|null $trimCharMask
     *
     * @return StringNormalizer
     */
    public function setTrimCharMask($trimCharMask)
    {
        $this->trimCharMask = $trimCharMask;

        return $this;
    }

    /**
     * @return string
     */
    public function getTrimCharMask()
    {
        return $this->trimCharMask;
    }

    /**
     * @return bool
     */
    public function shouldTrim()
    {
        return $this->trim;
    }

    protected function getNormalizedValue($value)
    {
        if ($this->shouldTrim()) {
            $value = \trim($value, $this->getTrimCharMask());
        }

        return $value;
    }
}
