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

    public function getReader(): Reader
    {
        return $this->reader;
    }

    public function setReader(Reader $reader): self
    {
        $this->reader = $reader;

        return $this;
    }

    /**
     * @return Cell[]
     */
    public function getCells(): array
    {
        return $this->cells;
    }

    public function addCells(array $cells): void
    {
        foreach ($cells as $key => $value) {
            $key = $this->reader->getHeader($key);
            $this->addCell($key, $value);
        }
    }

    public function addCell($key, $value): void
    {
        $normalizer = $this->reader->getNormalizer($key);

        $cell = new Cell($value, $normalizer);

        $this->cells[$key] = $cell;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
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
    public function offsetSet($offset, $value): void
    {
        $this->addCell($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->cells[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return \count($this->cells);
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->cells);
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->cells as $key => $cell) {
            $result[$key] = $cell->getValue();
        }

        return $result;
    }
}
