<?php

namespace Palmtree\Csv\Test\Formatter;

use Palmtree\Csv\Formatter\ArrayFormatter;
use Palmtree\Csv\Formatter\NumberFormatter;
use PHPUnit\Framework\TestCase;

class ArrayFormatterTest extends TestCase
{
    public function testFormatterReturnsArray()
    {
        $formatter = new ArrayFormatter(new NumberFormatter(), ',');

        $value = $formatter->format('1,2,3');

        $this->assertTrue(is_array($value));
        $this->assertSame([1, 2, 3], $value);
    }
}
