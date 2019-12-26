<?php

namespace Palmtree\Csv\Test;

use Palmtree\Csv\Reader;
use Palmtree\Csv\Util\StringUtil;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{
    public function testInvalidFile(): void
    {
        $this->expectException('RuntimeException');
        $reader = new Reader('foo.bar');
        $reader->createDocument();
    }

    public function testMultipleIterations(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');

        $asserted = false;
        foreach ($reader as $row) {
            if (!$asserted) {
                $this->assertArrayHasKey('product_id', $row);
                $asserted = true;
            }
        }

        $asserted = false;
        foreach ($reader as $row) {
            if (!$asserted) {
                $this->assertArrayHasKey('product_id', $row);
                $asserted = true;
            }
        }
    }

    public function testBomStripping(): void
    {
        // Load our BOM prefixed file.
        $reader = new Reader(__DIR__ . '/fixtures/products-bom.csv');

        $header = $reader->getHeaders()[0];

        $this->assertFalse(StringUtil::hasBom($header, StringUtil::BOM_UTF8), 'UTF-8 BOM was stripped');
    }

    public function testNoBomStripping(): void
    {
        // Load our BOM prefixed file.
        $reader = new Reader(__DIR__ . '/fixtures/products-bom.csv');
        $reader->setStripBom(null);

        $header = $reader->getHeaders()[0];

        $this->assertTrue(StringUtil::hasBom($header, StringUtil::BOM_UTF8), 'UTF-8 BOM was not stripped');
    }

    public function testHeaderOffset(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products-offset.csv');
        $reader
            ->setHeaderOffset(1)
            ->setOffset(1);

        $headers = $reader->getHeaders()->toArray();

        $this->assertContains('product_id', $headers);

        $asserted = false;
        foreach ($reader as $row) {
            if (!$asserted) {
                $this->assertArrayHasKey('description', $row);
                $asserted = true;
            }
        }
    }

    public function testNewLines(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/newlines.csv');
        $reader->setHasHeaders(false);
        $rows = $reader->toArray();

        $this->assertCount(2, $rows);
        $this->assertEquals("Hello\nWorld", $rows[0][0]);
        $this->assertEquals("Foo\nBar", $rows[1][0]);
    }
}
