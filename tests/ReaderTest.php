<?php

namespace Palmtree\Csv\Test;

use Palmtree\Csv\Reader;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{
    /** @expectedException \Exception */
    public function testInvalidFile()
    {
        $reader = new Reader('foo.bar');
        $reader->createDocument();
    }
}
