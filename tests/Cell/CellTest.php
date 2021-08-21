<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test\Cell;

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Normalizer\BooleanNormalizer;
use Palmtree\Csv\Normalizer\NumberNormalizer;
use PHPUnit\Framework\TestCase;

class CellTest extends TestCase
{
    public function testCellFormatting(): void
    {
        $cell = new Cell('1', new NumberNormalizer());
        $this->assertSame(1, $cell->getValue());
        $this->assertSame('1', $cell->getRawValue());

        $cell = new Cell('true', new BooleanNormalizer());
        $this->assertTrue($cell->getValue());
        $this->assertSame('true', $cell->getRawValue());
    }
}
