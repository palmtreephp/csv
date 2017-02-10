<?php

namespace Palmtree\Csv;

use Palmtree\Csv\Cell\Cell;

class Row implements \ArrayAccess, \Countable, \Serializable
{
    /** @var Cell[] $cells */
    protected $cells = [];
    /** @var Reader $reader */
    protected $reader;

    public function __construct($cells, Reader $reader)
    {
        $this->reader = $reader;

        $headers = $this->reader->getHeaders();

        foreach ($cells as $key => $value) {
            if (isset($headers[$key])) {
                $key = $headers[$key];
            }

            $this->addCell($key, $value);
        }
    }

    public function getCells()
    {
        return $this->cells;
    }

    public function addCell($key, $value)
    {
        $formatter = $this->reader->getFormatter($key);

        $class = (isset($formatter::$cellClass)) ? $formatter::$cellClass : Cell::class;
        $cell  = new $class($value, $this->reader->getFormatter($key));

        $this->cells[$key] = $cell;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->cells);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->cells[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->cells[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->cells[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->cells);
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize($this->cells);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        return unserialize($this->cells);
    }
}
