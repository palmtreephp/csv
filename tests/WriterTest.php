<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test;

use Palmtree\Csv\InlineReader;
use Palmtree\Csv\Writer;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase
{
    public function testGetContents(): void
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
        $rows = $reader->toArray();

        $row = $rows[0];

        $this->assertArrayHasKey('first', $row);
        $this->assertArrayHasKey('second', $row);
        $this->assertSame('foo', $row['first']);
        $this->assertSame('bar', $row['second']);
    }

    public function testFirstCallToAddRowSetsHeaders(): void
    {
        $writer = new Writer('php://memory');

        $writer->addRow([
            'foo' => 'bar',
            'baz' => 'qux',
        ]);

        $writer->addRow([
            'foo' => 'bar2',
            'baz' => 'qux2',
        ]);

        $reader = new InlineReader($writer->getContents());
        $rows = $reader->toArray();

        $row = $rows[0];

        $this->assertArrayHasKey('foo', $row);
        $this->assertArrayHasKey('baz', $row);
        $this->assertSame('bar', $row['foo']);
        $this->assertSame('qux', $row['baz']);
    }

    public function testZeroSizeDocumentReturnsEmptyString(): void
    {
        $writer = new Writer('php://memory');

        $this->assertEmpty($writer->getContents());
    }
}
