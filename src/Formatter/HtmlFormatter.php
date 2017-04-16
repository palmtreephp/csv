<?php

namespace Palmtree\Csv\Formatter;

/**
 */
class HtmlFormatter extends AbstractFormatter
{
    protected $encode = true;
    protected $flags = ENT_QUOTES;

    /**
     * @return bool
     */
    public function isEncode()
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

    protected function getFormattedValue($value)
    {
        if ($this->isEncode()) {
            $value = htmlentities($value, $this->getFlags());
        } else {
            $value = html_entity_decode($value, $this->getFlags());
        }

        return $value;
    }
}
