<?php

declare(strict_types=1);

namespace Palmtree\Csv\Row;

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Reader;

class Row implements \ArrayAccess, \Countable, \IteratorAggregate
{
    private Reader $reader;
    /** @var array<Cell> */
    private array $cells = [];

    public function __construct(array $cells, Reader $reader)
    {
        $this->reader = $reader;
        $this->addCells($cells);
    }

    public function getReader(): Reader
    {
        return $this->reader;
    }

    /**
     * @return array<Cell>
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

    /** @param string|int $key */
    public function addCell($key, string $value): void
    {
        $normalizer = $this->reader->getNormalizer($key);

        $cell = new Cell($value, $normalizer);

        $this->cells[$key] = $cell;
    }

    /**
     * {@inheritDoc}
     *
     * @param string|int $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->cells[$offset]) || \array_key_exists($offset, $this->cells);
    }

    /**
     * {@inheritDoc}
     *
     * @param string|int $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->cells[$offset]->getValue();
    }

    /**
     * {@inheritDoc}
     *
     * @param string|int $offset
     * @param string     $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->addCell($offset, $value);
    }

    /**
     * {@inheritDoc}
     *
     * @param string|int $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->cells[$offset]);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return \count($this->cells);
    }

    /**
     * {@inheritDoc}
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
