<?php

namespace Palmtree\Csv\Normalizer;

class HtmlNormalizer extends AbstractNormalizer
{
    /** @var bool */
    private $encode = true;
    /** @var int */
    private $flags  = ENT_QUOTES;

    /**
     * @return bool
     */
    public function shouldEncode()
    {
        return $this->encode;
    }

    /**
     * @param bool $encode
     *
     * @return self
     */
    public function setEncode($encode)
    {
        $this->encode = (bool)$encode;

        return $this;
    }

    /**
     * @param int $flags
     *
     * @return self
     */
    public function setFlags($flags)
    {
        $this->flags = (int)$flags;

        return $this;
    }

    /**
     * @return int
     */
    public function getFlags()
    {
        return $this->flags;
    }

    protected function getNormalizedValue($value)
    {
        if ($this->shouldEncode()) {
            $value = \htmlentities($value, $this->getFlags());
        } else {
            $value = \html_entity_decode($value, $this->getFlags());
        }

        return $value;
    }
}
