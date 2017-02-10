<?php

namespace Palmtree\Csv\Formatter;

class FilterFormatter extends AbstractFormatter
{
    protected $callback;

    public function __construct(callable $callback, $formatter = null)
    {
        if (! is_callable($callback)) {
            throw new \InvalidArgumentException(sprintf('%s requires a valid callback', __CLASS__));
        }

        parent::__construct($formatter);
    }

    protected function getFormattedValue($value)
    {
        $value = call_user_func($this->callback);

        return $value;
    }

}
