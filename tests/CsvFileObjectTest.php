<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test;

use Palmtree\Csv\CsvFileObject;
use PHPUnit\Framework\TestCase;

class CsvFileObjectTest extends TestCase
{
    public function testGetLineEnding(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'phpunit_csv_');
        $file = new CsvFileObject($tempFile, 'w');

        $this->assertSame("\r\n", $file->getLineEnding());

        unlink($tempFile);
    }

    public function testSetLineEnding(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'phpunit_csv_');
        $file = new CsvFileObject($tempFile, 'w');

        $result = $file->setLineEnding("\n");

        $this->assertSame($file, $result);
        $this->assertSame("\n", $file->getLineEnding());

        unlink($tempFile);
    }

    public function testGetBytesWrittenInitial(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'phpunit_csv_');
        $file = new CsvFileObject($tempFile, 'w');

        $this->assertSame(0, $file->getBytesWritten());

        unlink($tempFile);
    }

    public function testCsvFileObjectIsInstanceOfSplFileObject(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'phpunit_csv_');
        $file = new CsvFileObject($tempFile, 'w');

        // Test actual SplFileObject functionality
        $this->assertTrue($file->isFile());

        unlink($tempFile);
    }

    public function testCsvFileObjectExtendsCorrectClass(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'phpunit_csv_');
        $file = new CsvFileObject($tempFile, 'w');

        // CsvFileObject is successfully instantiated
        $this->assertEquals(CsvFileObject::class, $file::class);

        unlink($tempFile);
    }
}
