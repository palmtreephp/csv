<?php

namespace Palmtree\Csv\Cell;

use Palmtree\Csv\Formatter\FormatterInterface;

class Cell
{
    /** @var FormatterInterface $formatter */
    protected $formatter;
    protected $value;

    /**
     * Cell constructor.
     *
     * @param                         $value
     * @param FormatterInterface|null $formatter
     */
    public function __construct($value, FormatterInterface $formatter = null)
    {
        $this->value = $value;
        $this->setFormatter($formatter);
    }

    /**
     * @return string
     */
    public function getRawValue()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->getFormatter()->format($this->getRawValue());
    }

    /**
     * @return FormatterInterface
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * @param FormatterInterface $formatter
     *
     * @return Cell
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;

        return $this;
    }

    public function __toString()
    {
        try {
            $value = (string)$this->getValue();
        } catch (\Exception $exception) {
            $value = '';
        }

        return $value;
    }
}