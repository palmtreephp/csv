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

    public function testAddRowWithNumericArray(): void
    {
        $writer = new Writer('php://memory');
        $writer->setHeaders(['col1', 'col2', 'col3']);

        $result = $writer->addRow(['val1', 'val2', 'val3']);

        $this->assertTrue($result);
        $this->assertStringContainsString('val1', $writer->getContents());
    }

    public function testAddRowsWithMultipleRows(): void
    {
        $writer = new Writer('php://memory');
        $writer->setHeaders(['name', 'age']);

        $rows = [
            ['John', '30'],
            ['Jane', '25'],
            ['Bob', '35'],
        ];

        $writer->addRows($rows);

        $contents = $writer->getContents();
        $this->assertStringContainsString('John', $contents);
        $this->assertStringContainsString('Jane', $contents);
        $this->assertStringContainsString('Bob', $contents);
    }

    public function testAddHeader(): void
    {
        $writer = new Writer('php://memory');
        $writer->setHeaders(['first', 'second']);

        $result = $writer->addHeader('third');

        $this->assertSame($writer, $result);
    }

    public function testSetDelimiter(): void
    {
        $writer = new Writer('php://memory');
        $writer->setDelimiter(';');
        $writer->setHeaders(['first', 'second']);
        $writer->addRow(['foo', 'bar']);

        $contents = $writer->getContents();

        $this->assertStringContainsString(';', $contents);
        $this->assertStringNotContainsString(',"', $contents);
    }

    public function testSetEnclosure(): void
    {
        $writer = new Writer('php://memory');
        $writer->setEnclosure("'");
        $writer->setHeaders(['first', 'second']);
        $writer->addRow(['foo', 'bar']);

        $contents = $writer->getContents();

        $this->assertStringContainsString("'first'", $contents);
    }

    public function testWriterWithoutHeaders(): void
    {
        $writer = new Writer('php://memory', false);
        $writer->addRow(['value1', 'value2']);

        $contents = $writer->getContents();

        // Without headers mode, should just have the data
        $this->assertStringContainsString('value1', $contents);
    }

    public function testStaticWriteMethod(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'phpunit_writer_');

        $data = [
            ['name' => 'John', 'age' => '30'],
            ['name' => 'Jane', 'age' => '25'],
        ];

        Writer::write($tempFile, $data);

        $this->assertFileExists($tempFile);
        $contents = file_get_contents($tempFile);
        $this->assertIsString($contents);
        $this->assertStringContainsString('John', $contents);

        unlink($tempFile);
    }

    public function testSetFilePath(): void
    {
        $writer = new Writer('php://memory');
        $newPath = 'php://memory2';

        $result = $writer->setFilePath($newPath);

        $this->assertSame($writer, $result);
        $this->assertSame($newPath, $writer->getFilePath());
    }

    public function testGetDelimiter(): void
    {
        $writer = new Writer('php://memory');

        $delimiter = $writer->getDelimiter();

        $this->assertSame(',', $delimiter);
    }

    public function testGetEnclosure(): void
    {
        $writer = new Writer('php://memory');

        $enclosure = $writer->getEnclosure();

        $this->assertSame('"', $enclosure);
    }

    public function testSetEscapeCharacter(): void
    {
        $writer = new Writer('php://memory');

        $result = $writer->setEscapeCharacter('\\');

        $this->assertSame($writer, $result);
    }

    public function testGetEscapeCharacter(): void
    {
        $writer = new Writer('php://memory');

        $escapeChar = $writer->getEscapeCharacter();

        $this->assertSame("\0", $escapeChar);
    }

    public function testAddHeaderMethod(): void
    {
        $writer = new Writer('php://memory');
        $writer->setHeaders(['first']);
        $writer->addHeader('second');

        $contents = $writer->getContents();
        $this->assertStringContainsString('second', $contents);
    }

    public function testSetData(): void
    {
        $writer = new Writer('php://memory');

        $data = [
            ['name' => 'John', 'age' => '30'],
            ['name' => 'Jane', 'age' => '25'],
        ];

        $writer->setData($data);
        $contents = $writer->getContents();

        $this->assertStringContainsString('name', $contents);
        $this->assertStringContainsString('age', $contents);
        $this->assertStringContainsString('John', $contents);
        $this->assertStringContainsString('Jane', $contents);
    }
}
