<?php

namespace Palmtree\Csv\Test\Cell;

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Formatter\BooleanFormatter;
use Palmtree\Csv\Formatter\NumberFormatter;
use PHPUnit\Framework\TestCase;

class CellTest extends TestCase
{
    public function testCellFormatting()
    {
        $cell = new Cell('1', new NumberFormatter());
        $this->assertSame(1, $cell->getValue());
        $this->assertSame('1', $cell->getRawValue());

        $cell = new Cell('true', new BooleanFormatter());
        $this->assertTrue($cell->getValue());
        $this->assertSame('true', $cell->getRawValue());
    }
}
