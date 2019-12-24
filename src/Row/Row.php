<?php

namespace Palmtree\Csv\Row;

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Reader;

class Row implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /** @var Reader $reader */
    private $reader;
    /** @var Cell[] $cells */
    private $cells = [];

    public function __construct(array $cells, Reader $reader)
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
     * @return self
     */
    public function setReader(Reader $reader)
    {
        $this->reader = $reader;

        return $this;
    }

    /**
     * @return Cell[]
     */
    public function getCells()
    {
        return $this->cells;
    }

    public function addCells(array $cells)
    {
        foreach ($cells as $key => $value) {
            $key = $this->getReader()->getHeader($key);
            $this->addCell($key, $value);
        }
    }

    public function addCell($key, $value)
    {
        $normalizer = $this->getReader()->getNormalizer($key);

        $cell = new Cell($value, $normalizer);

        $this->cells[$key] = $cell;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->cells[$offset]) || \array_key_exists($offset, $this->cells);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->cells[$offset]->getValue();
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
        return \count($this->cells);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->cells);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];

        foreach ($this->cells as $key => $cell) {
            $result[$key] = $cell->getValue();
        }

        return $result;
    }
}
