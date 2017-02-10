<?php

namespace Palmtree\Csv\Cell;

use Palmtree\Csv\Formatter\FormatterInterface;

class ArrayCell extends Cell implements \ArrayAccess, \Iterator, \Countable, \Serializable
{
    protected $value;
    protected $index = 0;
    protected $formattedValue;

    public function __construct($value, FormatterInterface $formatter = null)
    {
        parent::__construct($value, $formatter);

        $this->formattedValue = $this->formatter->format($value);
    }

    public function getValue()
    {
        return $this->formattedValue;
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
    public function current()
    {
        return $this->formattedValue[$this->index];
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        ++$this->index;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return isset($this->formattedValue[$this->index]);
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->index = 0;
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
        return implode(',', $this->getValue());
    }
}
