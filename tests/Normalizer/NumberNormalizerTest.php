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

    public function testFloats()
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('1.234');

        $this->assertSame(1.234, $value);
    }

    public function testIntegers()
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('100');

        $this->assertSame(100, $value);
    }

    public function testRounding()
    {
        $normalizer = new NumberNormalizer(null, 2);

        $value = $normalizer->normalize(M_PI);

        $this->assertSame(3.14, $value);
    }
}
