<?php

namespace Palmtree\Csv\Test\Normalizer;

use Palmtree\Csv\Normalizer\NumberNormalizer;
use PHPUnit\Framework\TestCase;

class NumberNormalizerTest extends TestCase
{
    public function testNormalizerReturnsNumber()
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('0123');

        $this->assertSame(123, $value);
    }
}
