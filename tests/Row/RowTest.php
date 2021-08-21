<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test\Row;

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Reader;
use Palmtree\Csv\Row\Row;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    public function testRowArrayAccess(): void
    {
        $reader = new Reader('php://memory');

        $row = new Row(['foo', 'bar'], $reader);

        $this->assertSame('bar', $row[1]);

        $row[2] = 'baz';

        $cells = $row->getCells();

        $this->assertInstanceOf(Cell::class, $cells[2]);
        $this->assertSame('baz', $cells[2]->getValue());

        unset($row[2]);

        $cells = $row->getCells();

        $this->assertArrayNotHasKey(2, $cells);
    }

    public function testRowIsCountable(): void
    {
        $reader = new Reader('php://memory');

        $row = new Row(['foo', 'bar'], $reader);

        $this->assertCount(2, $row);
    }

    public function testRowIsTraversable(): void
    {
        $reader = new Reader('php://memory');

        $row = new Row(['foo', 'bar'], $reader);

        $this->assertInstanceOf('Traversable', $row);

        foreach ($row as $i => $cell) {
            $this->assertSame($i ? 'bar' : 'foo', $cell->getValue());
        }
    }

    public function testCanGetReader(): void
    {
        $reader = new Reader('php://memory');

        $row = new Row(['foo', 'bar'], $reader);

        $this->assertInstanceOf(Reader::class, $row->getReader());
        $this->assertSame($reader, $row->getReader());
    }
}
