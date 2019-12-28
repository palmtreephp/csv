<?php

namespace Palmtree\Csv\Test\Normalizer;

use Palmtree\Csv\Normalizer\ArrayNormalizer;
use Palmtree\Csv\Normalizer\NumberNormalizer;
use PHPUnit\Framework\TestCase;

class ArrayNormalizerTest extends TestCase
{
    public function testNormalizerReturnsArray(): void
    {
        $normalizer = new ArrayNormalizer(new NumberNormalizer(), ',');

        $value = $normalizer->normalize('1,2,3');

        $this->assertTrue(\is_array($value));
        $this->assertSame([1, 2, 3], $value);
    }
}
