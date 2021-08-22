<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test\Normalizer;

use Palmtree\Csv\Normalizer\NumberNormalizer;
use PHPUnit\Framework\TestCase;

class NumberNormalizerTest extends TestCase
{
    public function testNormalizerReturnsNumber(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('0123');

        $this->assertSame(123, $value);
    }

    public function testFloats(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('1.234');

        $this->assertSame(1.234, $value);
    }

    public function testIntegers(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('100');

        $this->assertSame(100, $value);
    }

    public function testRounding(): void
    {
        $normalizer = new NumberNormalizer(null);
        $normalizer->scale(2);

        $value = $normalizer->normalize((string)\M_PI);

        $this->assertSame(3.14, $value);
    }
}
