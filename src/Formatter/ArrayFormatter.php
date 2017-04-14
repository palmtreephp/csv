<?php

namespace Palmtree\Csv\Formatter;

use Palmtree\Csv\Cell\ArrayCell;

class ArrayFormatter extends AbstractFormatter
{
    protected $delimiter;

    protected $stringFormatter;

    const CELL_CLASS = ArrayCell::class;

    /**
     * ArrayFormatter constructor.
     *
     * @param null|FormatterInterface $formatter
     * @param string                  $delimiter
     */
    public function __construct($formatter = null, $delimiter = ',')
    {
        $this->setDelimiter($delimiter);

        $this->stringFormatter = new StringFormatter();
        $this->stringFormatter->setTrimCharMask($this->stringFormatter->getTrimCharMask() . $this->getDelimiter());

        parent::__construct($formatter);
    }

    public function format($value)
    {
        return $this->getFormattedValue($value);
    }

    public function getFormattedValue($value)
    {
        $value          = $this->stringFormatter->format($value);
        $formattedValue = explode($this->getDelimiter(), $value);

        foreach ($formattedValue as &$part) {
            $part = $this->getFormatter()->format($part);
        }

        return $formattedValue;
    }

    /**
     * @param string $delimiter
     *
     * @return ArrayFormatter
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
