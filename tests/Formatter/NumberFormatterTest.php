<?php

namespace Palmtree\Csv\Test\Formatter;

use Palmtree\Csv\Formatter\NumberFormatter;
use PHPUnit\Framework\TestCase;

class NumberFormatterTest extends TestCase
{
    public function testFormatterReturnsNumber()
    {
        $formatter = new NumberFormatter();

        $value = $formatter->format('0123');

        $this->assertSame(123, $value);
    }
}
