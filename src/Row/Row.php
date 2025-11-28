<?php

declare(strict_types=1);

namespace Palmtree\Csv\Row;

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Reader;

/**
 * @implements \ArrayAccess<string|int, mixed>
 * @implements \IteratorAggregate<int|string, Cell>
 */
class Row implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /** @var array<Cell> */
    private array $cells = [];

    public function __construct(array $cells, private readonly Reader $reader)
    {
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

    public function addCell(int|string $key, string $value): void
    {
        $normalizer = $this->reader->getNormalizer($key);

        $cell = new Cell($value, $normalizer);

        $this->cells[$key] = $cell;
    }

    /**
     * @param string|int $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->cells[$offset]) || \array_key_exists($offset, $this->cells);
    }

    /**
     * @param string|int $offset
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->cells[$offset]->getValue();
    }

    /**
     * @param string|int $offset
     * @param string     $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->addCell($offset, $value);
    }

    /**
     * @param string|int $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->cells[$offset]);
    }

    public function count(): int
    {
        return \count($this->cells);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->cells);
    }

    public function toArray(): array
    {
        return array_map(fn ($cell) => $cell->getValue(), $this->cells);
    }
}
