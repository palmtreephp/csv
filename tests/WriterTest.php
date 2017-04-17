<?php

namespace Palmtree\Csv\Test;

use Palmtree\Csv\Writer;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase
{
    /** @expectedException \Exception */
    public function testInvalidFile()
    {
        $writer = new Writer(null);
        $writer->createDocument();
    }
}
