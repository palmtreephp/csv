<?php

namespace Palmtree\Csv\Formatter;

class StringFormatter extends AbstractFormatter
{
    protected $trim = true;
    public $trimCharMask = " \t\n\r\0\x0B";

    public function __construct($formatter = null, $trim = true, $trimCharMask = null)
    {
        parent::__construct($formatter);

        $this->trim = $trim;

        if ($trimCharMask) {
            $this->trimCharMask = $trimCharMask;
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

    protected function getFormattedValue($value)
    {
        if ($this->trim) {
            $value = trim($value, $this->trimCharMask);
        }

        return $value;
    }
}
