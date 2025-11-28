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

    public function testNegativeIntegers(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('-100');

        $this->assertSame(-100, $value);
    }

    public function testNegativeFloats(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('-3.14');

        $this->assertSame(-3.14, $value);
    }

    public function testLeadingZeros(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('00000123');

        $this->assertSame(123, $value);
    }

    public function testZero(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('0');

        $this->assertSame(0, $value);
    }

    public function testNonNumericValueReturnsZero(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('not a number');

        $this->assertSame(0, $value);
    }

    public function testEmptyStringReturnsZero(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('');

        $this->assertSame(0, $value);
    }

    public function testLeadingDecimalPoint(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('0.5');

        $this->assertSame(0.5, $value);
    }

    public function testNegativeLeadingDecimalPoint(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('-0.5');

        $this->assertSame(-0.5, $value);
    }

    public function testScaleReturnsInstance(): void
    {
        $normalizer = new NumberNormalizer();

        $result = $normalizer->scale(2);

        $this->assertSame($normalizer, $result);
    }

    public function testRoundingWithHigherScale(): void
    {
        $normalizer = new NumberNormalizer();
        $normalizer->scale(4);

        $value = $normalizer->normalize('3.141592653589793');

        $this->assertSame(3.1416, $value);
    }

    public function testRoundingWithZeroScale(): void
    {
        $normalizer = new NumberNormalizer();
        $normalizer->scale(0);

        $value = $normalizer->normalize('3.7');

        // round() with scale 0 returns float, so use assertEquals instead of assertSame
        $this->assertEquals(4, $value);
    }

    public function testWhitespaceHandling(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('  123.45  ');

        $this->assertSame(123.45, $value);
    }

    public function testVeryLargeNumbers(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('999999999999999');

        $this->assertSame(999999999999999, $value);
    }

    public function testVerySmallDecimals(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('0.001');

        $this->assertSame(0.001, $value);
    }

    public function testNegativeScientificNotation(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('-1.5e-3');

        $this->assertSame(-0.0015, $value);
    }

    public function testPositiveScientificNotation(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('1.5e2');

        $this->assertSame(150.0, $value);
    }

    public function testRoundingWithHighDecimalPlaces(): void
    {
        $normalizer = new NumberNormalizer();
        $normalizer->scale(5);

        $value = $normalizer->normalize('3.123456789');

        $this->assertEquals(3.12346, $value);
    }

    public function testZeroWithLeadingDecimals(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('0.0');

        $this->assertSame(0.0, $value);
    }

    public function testNegativeZero(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('-0');

        $this->assertSame(0, $value);
    }

    public function testAllZerosWithDifferentFormat(): void
    {
        $normalizer = new NumberNormalizer();

        $value1 = $normalizer->normalize('0000');
        $value2 = $normalizer->normalize('0.00');

        $this->assertSame(0, $value1);
        $this->assertSame(0.0, $value2);
    }

    public function testNonNumericReturnsZero(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('abc');

        $this->assertSame(0, $value);
    }

    public function testWhitespaceOnlyReturnsZero(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('   ');

        $this->assertSame(0, $value);
    }

    public function testNumberWithWhitespace(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('  123.45  ');

        $this->assertSame(123.45, $value);
    }

    public function testScientificNotation(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('1e2');

        $this->assertSame(100.0, $value);
    }

    public function testScientificNotationNegativeExponent(): void
    {
        $normalizer = new NumberNormalizer();

        $value = $normalizer->normalize('1e-2');

        $this->assertSame(0.01, $value);
    }

    public function testRoundingWithDifferentScales(): void
    {
        $normalizer = new NumberNormalizer();
        $normalizer->scale(3);

        $value = $normalizer->normalize('3.14159');

        $this->assertSame(3.142, $value);
    }

    public function testRoundingToZeroDecimalPlaces(): void
    {
        $normalizer = new NumberNormalizer();
        $normalizer->scale(0);

        $value = $normalizer->normalize('3.7');

        $this->assertSame(4.0, $value);
    }

    public function testScaleWithNegativeNumber(): void
    {
        $normalizer = new NumberNormalizer();
        $normalizer->scale(2);

        $value = $normalizer->normalize('-3.14159');

        $this->assertSame(-3.14, $value);
    }

    public function testChangingScaleMultipleTimes(): void
    {
        $normalizer = new NumberNormalizer();

        $normalizer->scale(1);
        $value1 = $normalizer->normalize('1.234');
        $this->assertSame(1.2, $value1);

        $normalizer->scale(3);
        $value2 = $normalizer->normalize('1.234');
        $this->assertSame(1.234, $value2);
    }

    public function testFluentInterface(): void
    {
        $normalizer = new NumberNormalizer();
        $result = $normalizer->scale(2);

        $this->assertSame($normalizer, $result);
    }

    public function testResettingScaleToNull(): void
    {
        $normalizer = new NumberNormalizer();
        $normalizer->scale(2);
        $normalizer->scale(null);

        $value = $normalizer->normalize('1.23456789');

        $this->assertSame(1.23456789, $value);
    }
}
