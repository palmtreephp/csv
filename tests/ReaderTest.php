<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test;

use Palmtree\Csv\Normalizer\NumberNormalizer;
use Palmtree\Csv\Reader;
use Palmtree\Csv\Util\StringUtil;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{
    public function testInvalidFile(): void
    {
        $this->expectException('RuntimeException');
        $reader = new Reader('foo.bar');
        $reader->getDocument();
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

    public function testUsingTabAsDelimiter(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/tab-delimiter.tsv');
        $reader->setHasHeaders(false);
        $reader->setDelimiter("\t");

        $row = $reader->toArray()[0];

        $this->assertCount(3, $row);
        $this->assertEquals('Foo', $row[0]);
        $this->assertEquals('Bar', $row[1]);
        $this->assertEquals('Baz', $row[2]);
    }

    public function testStaticReadMethod(): void
    {
        $reader = Reader::read(__DIR__ . '/fixtures/products.csv');

        $this->assertTrue($reader->hasHeaders());
    }

    public function testStaticReadMethodWithoutHeaders(): void
    {
        $reader = Reader::read(__DIR__ . '/fixtures/products.csv', false);

        $this->assertFalse($reader->hasHeaders());
    }

    public function testHasHeadersGetterSetter(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv', true);

        $this->assertTrue($reader->hasHeaders());

        $reader->setHasHeaders(false);

        $this->assertFalse($reader->hasHeaders());
    }

    public function testGetHeadersReturnsRowObject(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');
        $headers = $reader->getHeaders();

        $this->assertNotEmpty($headers->toArray());
    }

    public function testAddNormalizerForColumn(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');
        $reader->addNormalizer('product_id', new NumberNormalizer());

        foreach ($reader as $row) {
            $this->assertIsInt($row['product_id']);
            break;
        }
    }

    public function testAddNormalizers(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');
        $normalizers = [
            'product_id' => new NumberNormalizer(),
            'name' => new \Palmtree\Csv\Normalizer\StringNormalizer(),
        ];

        $reader->addNormalizers($normalizers);

        foreach ($reader as $row) {
            $this->assertIsInt($row['product_id']);
            $this->assertIsString($row['name']);
            break;
        }
    }

    public function testSetDefaultNormalizer(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');
        $reader->setDefaultNormalizer(NumberNormalizer::class);

        $this->assertSame(NumberNormalizer::class, $reader->getDefaultNormalizer());
    }

    public function testGetNormalizer(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');
        $normalizer = new NumberNormalizer();
        $reader->addNormalizer('product_id', $normalizer);

        $result = $reader->getNormalizer('product_id');

        $this->assertSame($normalizer, $result);
    }

    public function testSetHeaderNormalizer(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');
        $normalizer = new \Palmtree\Csv\Normalizer\StringNormalizer();
        $reader->setHeaderNormalizer($normalizer);

        $this->assertSame($normalizer, $reader->getHeaderNormalizer());
    }

    public function testGetHeaderNormalizerDefault(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');
        $normalizer = $reader->getHeaderNormalizer();

        $this->assertInstanceOf(\Palmtree\Csv\Normalizer\NullNormalizer::class, $normalizer);
    }

    public function testGetDelimiter(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');

        $this->assertSame(',', $reader->getDelimiter());
    }

    public function testSetDelimiter(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv', false);
        $reader->setDelimiter('|');

        $this->assertSame('|', $reader->getDelimiter());
    }

    public function testGetEnclosure(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');

        $this->assertSame('"', $reader->getEnclosure());
    }

    public function testSetEnclosure(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv', false);
        $reader->setEnclosure("'");

        $this->assertSame("'", $reader->getEnclosure());
    }

    public function testGetEscapeCharacter(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');

        $this->assertSame("\0", $reader->getEscapeCharacter());
    }

    public function testSetEscapeCharacter(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv', false);
        $reader->setEscapeCharacter('\\');

        $this->assertSame('\\', $reader->getEscapeCharacter());
    }

    public function testGetFilePath(): void
    {
        $path = __DIR__ . '/fixtures/products.csv';
        $reader = new Reader($path);

        $this->assertSame($path, $reader->getFilePath());
    }

    public function testSetFilePath(): void
    {
        $reader = new Reader('php://memory');
        $newPath = '/some/other/path.csv';

        $reader->setFilePath($newPath);

        $this->assertSame($newPath, $reader->getFilePath());
    }

    public function testOffsetGetterSetter(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');

        $this->assertSame(0, $reader->getOffset());

        $reader->setOffset(5);

        $this->assertSame(5, $reader->getOffset());
    }

    public function testHeaderOffsetGetterSetter(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');

        $this->assertSame(0, $reader->getHeaderOffset());

        $reader->setHeaderOffset(2);

        $this->assertSame(2, $reader->getHeaderOffset());
    }

    public function testFluentInterface(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');

        $result = $reader
            ->setDelimiter(',')
            ->setEnclosure('"')
            ->setHasHeaders(true);

        $this->assertSame($reader, $result);

        // Test Reader-specific methods
        $result2 = $reader->setOffset(0);
        $this->assertSame($reader, $result2);
    }

    public function testToArrayReturnsArrayOfRows(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');
        $rows = $reader->toArray();

        $this->assertNotEmpty($rows);
    }

    public function testCurrentReturnsRow(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');

        foreach ($reader as $row) {
            $this->assertNotEmpty($row->toArray());
            break;
        }
    }

    public function testKeyReturnsLineNumber(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');

        foreach ($reader as $key => $row) {
            $this->assertGreaterThanOrEqual(0, $key);
            break;
        }
    }

    public function testGetHeaderWithStringKey(): void
    {
        $reader = new Reader(__DIR__ . '/fixtures/products.csv');
        $headers = $reader->getHeaders();

        // Headers should not be null
        $this->assertNotNull($headers);
    }

    public function testCloseDocument(): void
    {
        $reader = new Reader('php://memory');
        $reader->closeDocument();

        // Should not throw exception when accessing after close
        $this->assertSame('php://memory', $reader->getFilePath());
    }
}
