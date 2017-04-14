<?php

namespace Palmtree\Csv\Formatter;

/**
 * StringFormatter formats a CSV cell as a string.
 * It will trim the string by default.
 */
class StringFormatter extends AbstractFormatter
{
    protected $trim = true;
    protected $trimCharMask = " \t\n\r\0\x0B";

    public function __construct($formatter = null, $trim = true, $trimCharMask = null)
    {
        parent::__construct($formatter);

        $this->setTrim($trim);

        if ($trimCharMask) {
            $this->setTrimCharMask($trimCharMask);
        }
    }

    /**
     * @param bool $trim
     *
     * @return StringFormatter
     */
    public function setTrim($trim)
    {
        $this->trim = $trim;

        return $this;
    }

    /**
     * @param null|string $trimCharMask
     *
     * @return StringFormatter
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
    public function isTrim()
    {
        return $this->trim;
    }

    protected function getFormattedValue($value)
    {
        if ($this->isTrim()) {
            $value = trim($value, $this->getTrimCharMask());
        }

        return $value;
    }
}
