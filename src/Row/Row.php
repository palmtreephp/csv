<?php

namespace Palmtree\Csv\Row;

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Reader;

class Row implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /** @var Reader $reader */
    protected $reader;
    /** @var Cell[] $cells */
    protected $cells = [];

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

    /**
     * @return Cell[]
     */
    public function getCells()
    {
        return $this->cells;
    }

    /**
     * @param array $cells
     */
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
