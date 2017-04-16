<?php

namespace Palmtree\Csv\Formatter;

use Palmtree\Csv\Reader;

abstract class AbstractFormatter implements FormatterInterface
{
    /** @var FormatterInterface */
    protected $formatter;

    /**
     * AbstractFormatter constructor.
     *
     * @param null|FormatterInterface $formatter
     */
    public function __construct($formatter = null)
    {
        if (!$formatter instanceof FormatterInterface) {
            $formatter = new NullFormatter();
        }

        $this->setFormatter($formatter);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    abstract protected function getFormattedValue($value);

    /**
     * @inheritdoc
     */
    public function format($value)
    {
        $value = $this->getFormatter()->format($value);

        $value = $this->getFormattedValue($value);

        return $value;
    }

    /**
     * @param FormatterInterface $formatter
     *
     * @return AbstractFormatter
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * @return FormatterInterface
     */
    public function getFormatter()
    {
        return $this->formatter;
    }
}
