<?php

namespace Palmtree\Csv\Test\Row;

use Palmtree\Csv\Reader;
use Palmtree\Csv\Row\Row;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    public function testRowArrayAccess(): void
    {
        $reader = new Reader('php://memory');

        $row = new Row(['foo', 'bar'], $reader);

        $this->assertEquals('bar', $row[1]);
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
}
