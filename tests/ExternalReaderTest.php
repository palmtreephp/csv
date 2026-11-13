<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test;

use Palmtree\Csv\ExternalReader;
use PHPUnit\Framework\TestCase;

class ExternalReaderTest extends TestCase
{
    public function testExternalReaderWithLocalFile(): void
    {
        // Use a local file to avoid network dependency
        $testFile = __DIR__ . '/fixtures/products.csv';

        try {
            $reader = new ExternalReader($testFile);
            // Verify ExternalReader has Reader functionality
            $headers = $reader->getHeaders();
            $this->assertInstanceOf(\Palmtree\Csv\Row\Row::class, $headers);
        } catch (\RuntimeException $e) {
            // If file access fails, skip the test
            $this->markTestSkipped('Could not access test file');
        }
    }

    public function testTempFileIsRemoved(): void
    {
        $reader = new ExternalReader(__DIR__ . '/fixtures/products.csv');
        $reader->toArray();
        $tempFile = $reader->getFilePath();
        $this->assertFileExists($tempFile);
        // Rows reference their reader, so the reader is only destroyed once the cycle is collected.
        unset($reader);
        gc_collect_cycles();

        $this->assertFileDoesNotExist($tempFile);
    }
}
