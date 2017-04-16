<?php

namespace Palmtree\Csv\Row;

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Reader;

class Row implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /** @var Cell[] $cells */
    protected $cells = [];
    /** @var Reader $reader */
    protected $reader;

    public function __construct($cells, Reader $reader)
    {
        $this->setReader($reader);
        $this->addCells($cells);
    }

    /**
     * @return Reader
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * @param Reader $reader
     *
     * @return $this
     */
    public function setReader(Reader $reader)
    {
        $this->reader = $reader;

        return $this;
    }

    public function getCells()
    {
        return $this->cells;
    }

    public function addCells(array $cells)
    {
        $headers = $this->getReader()->getHeaders();

        foreach ($cells as $key => $value) {
            if (isset($headers[$key])) {
                $key = $headers[$key];
            }

            $this->addCell($key, $value);
        }
    }

    public function addCell($key, $value)
    {
        $formatter = $this->getReader()->getFormatter($key);

        $cell = new Cell($value, $formatter);

        $this->cells[$key] = $cell;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->getCells());
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->getCells()[$offset]->getValue();
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->addCell($offset, $value);
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
        return count($this->getCells());
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getCells());
    }
}
