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
    /** @var list<Cell>|null */
    private ?array $positionalCells = null;

    /**
     * @param array<array-key, string> $cells
     */
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

    /**
     * @param array<array-key, string> $cells
     */
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
        $this->positionalCells = null;
    }

    /**
     * Returns the cell for the given key. In a row with no integer keys (i.e. keyed by header),
     * an integer offset is the cell's zero-based position, so the row can be destructured
     * positionally, e.g. foreach ($reader as [$name, $price]).
     */
    public function getCell(int|string $offset): ?Cell
    {
        if (isset($this->cells[$offset])) {
            return $this->cells[$offset];
        }

        if (!\is_int($offset)) {
            return null;
        }

        return $this->getPositionalCells()[$offset] ?? null;
    }

    /**
     * @param string|int $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->getCell($offset) !== null;
    }

    /**
     * @param string|int $offset
     */
    public function offsetGet(mixed $offset): mixed
    {
        $cell = $this->getCell($offset);

        if ($cell === null) {
            throw new \OutOfBoundsException(\sprintf('Row has no cell with key or position "%s".', $offset));
        }

        return $cell->getValue();
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
        $this->positionalCells = null;
    }

    public function count(): int
    {
        return \count($this->cells);
    }

    /**
     * @return \ArrayIterator<int|string, Cell>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->cells);
    }

    /**
     * @return list<Cell>
     */
    private function getPositionalCells(): array
    {
        if ($this->positionalCells === null) {
            $hasIntKeys = array_filter(array_keys($this->cells), is_int(...)) !== [];
            $this->positionalCells = $hasIntKeys ? [] : array_values($this->cells);
        }

        return $this->positionalCells;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function toArray(): array
    {
        return array_map(static fn ($cell) => $cell->getValue(), $this->cells);
    }
}
