<?php

namespace Palmtree\Csv\Test\Formatter;

use Palmtree\Csv\Formatter\StringFormatter;
use PHPUnit\Framework\TestCase;

class StringFormatterTest extends TestCase
{
    public function testFormatterTrimsStrings()
    {
        $formatter = new StringFormatter();
        $formatter->setTrim(true);

        $value = $formatter->format(' foo bar baz ');

        $this->assertSame('foo bar baz', $value);
    }

    public function testFormatterDoesNotTrimStrings()
    {
        $formatter = new StringFormatter();
        $formatter->setTrim(false);

        $value = $formatter->format(' foo bar baz ');

        $this->assertSame(' foo bar baz ', $value);
    }
}
