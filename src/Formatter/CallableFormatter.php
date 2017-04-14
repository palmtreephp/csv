<?php

namespace Palmtree\Csv\Formatter;

class CallableFormatter extends AbstractFormatter
{
    protected $callback;

    /**
     * CallableFormatter constructor.
     *
     * @param callable                $callback
     * @param null|FormatterInterface $formatter
     */
    public function __construct(callable $callback, $formatter = null)
    {
        $this->setCallback($callback);

        parent::__construct($formatter);
    }

    /**
     * @param callable $callback
     *
     * @return CallableFormatter
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    protected function getFormattedValue($value)
    {
        $value = call_user_func($this->getCallback(), $value);

        return $value;
    }
}
