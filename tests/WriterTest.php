<?php

namespace Palmtree\Csv\Test;

use Palmtree\Csv\InlineReader;
use Palmtree\Csv\Writer;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase
{
    public function testInvalidFile(): void
    {
        $this->expectException('TypeError');
        $writer = new Writer(null);
        $writer->getDocument();
    }

    public function testGetContents()
    {
        $writer = new Writer('php://memory');
        $writer->setHeaders(['first', 'second']);

        $writer->addRow(['foo', 'bar']);

        $this->assertSame('"first","second"' . "\r\n" . '"foo","bar"', $writer->getContents());
    }

    public function testSetHeaders(): void
    {
        $writer = new Writer('php://memory');
        $writer->setHeaders(['first', 'second']);

        $writer->addRow(['foo', 'bar']);

        $reader = new InlineReader($writer->getContents());
        $rows   = $reader->toArray();

        $row = $rows[0];

        $this->assertArrayHasKey('first', $row);
        $this->assertArrayHasKey('second', $row);
        $this->assertSame('foo', $row['first']);
        $this->assertSame('bar', $row['second']);
    }
}
