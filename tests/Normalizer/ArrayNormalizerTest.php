<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test\Normalizer;

use Palmtree\Csv\Normalizer\ArrayNormalizer;
use Palmtree\Csv\Normalizer\NumberNormalizer;
use PHPUnit\Framework\TestCase;

class ArrayNormalizerTest extends TestCase
{
    public function testNormalizerReturnsArray(): void
    {
        $normalizer = new ArrayNormalizer(new NumberNormalizer());

        $value = $normalizer->normalize('1,2,3');

        $this->assertSame([1, 2, 3], $value);
    }

    public function testDifferentDelimiter(): void
    {
        $normalizer = new ArrayNormalizer();
        $normalizer->delimiter('|');

        $value = $normalizer->normalize('1,2,3|4,5,6');

        $this->assertSame(['1,2,3', '4,5,6'], $value);
    }
}
