<?php

namespace Palmtree\Csv\Cell;

use Palmtree\Csv\Formatter\FormatterInterface;

class ArrayCell extends Cell implements \ArrayAccess, \IteratorAggregate, \Countable, \Serializable
{
    protected $value;
    protected $index = 0;
    /** @var mixed */
    protected $formattedValue;

    public function __construct($value, FormatterInterface $formatter)
    {
        parent::__construct($value, $formatter);

        $this->formattedValue = $this->formatter->format($value);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->formattedValue);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->formattedValue[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->formattedValue[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->formattedValue[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize($this->formattedValue);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $this->formattedValue = unserialize($serialized);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->formattedValue);
    }

    public function __toString()
    {
        try {
            $value = implode(',', $this->getValue());
        } catch (\Exception $exception) {
            $value = '';
        }

        return $value;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->formattedValue);
    }
}
