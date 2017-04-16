<?php

namespace Palmtree\Csv\Test\Row;

use Palmtree\Csv\Reader;
use Palmtree\Csv\Row\Row;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    public function testRowArrayAccess()
    {
        $reader = new Reader('php://memory', false);

        $row = new Row(['foo', 'bar'], $reader);

        $this->assertEquals('bar', $row[1]);
    }

    public function testRowCounting()
    {
        $reader = new Reader('php://memory', false);

        $row = new Row(['foo', 'bar'], $reader);

        $this->assertCount(2, $row);
    }

    public function testRowIsTraversable()
    {
        $reader = new Reader('php://memory', false);

        $row = new Row(['foo', 'bar'], $reader);

        $this->assertTrue(is_iterable($row));
    }
}
