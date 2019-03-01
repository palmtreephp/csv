<?php

namespace Palmtree\Csv\Test;

use Palmtree\Csv\Reader;
use Palmtree\Csv\Util\StringUtil;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{
    /** @expectedException \Exception */
    public function testInvalidFile()
    {
        $reader = new Reader('foo.bar');
        $reader->createDocument();
    }

    public function testMultipleIterations()
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

    public function testBomStripping()
    {
        // Load our BOM prefixed file.
        $reader = new Reader(__DIR__ . '/fixtures/products-bom.csv');
        $reader->setBom(true);

        $header = $reader->getHeaders()[0];

        $this->assertFalse(StringUtil::hasBom($header, StringUtil::BOM_UTF8), "UTF-8 BOM was not stripped");
    }

    public function testNoBomStripping()
    {
        // Load our BOM prefixed file.
        $reader = new Reader(__DIR__ . '/fixtures/products-bom.csv');
        $reader->setBom(false);

        $header = $reader->getHeaders()[0];

        $this->assertTrue(StringUtil::hasBom($header, StringUtil::BOM_UTF8), "UTF-8 BOM was not stripped");
    }

    public function testHeaderOffset()
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

    public function testNewLines()
    {
        $reader = new Reader(__DIR__ . '/fixtures/newlines.csv', false);
        $rows   = $reader->toArray();

        $this->assertEquals("Hello\nWorld", $rows[0][0]);
    }
}
