<?php

namespace Palmtree\Csv\Test;

use Palmtree\Csv\Writer;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase
{
    public function testInvalidFile(): void
    {
        $this->expectException('TypeError');
        $writer = new Writer(null);
        $writer->createDocument();
    }
}
