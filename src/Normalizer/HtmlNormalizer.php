<?php

namespace Palmtree\Csv\Normalizer;

/**
 */
class HtmlNormalizer extends AbstractNormalizer
{
    protected $encode = true;
    protected $flags = ENT_QUOTES;

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
     * @return $this
     */
    public function setEncode($encode)
    {
        $this->encode = (bool)$encode;

        return $this;
    }

    /**
     * @param int $flags
     *
     * @return $this
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
            $value = htmlentities($value, $this->getFlags());
        } else {
            $value = html_entity_decode($value, $this->getFlags());
        }

        return $value;
    }
}
