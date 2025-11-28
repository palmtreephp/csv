<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test\Normalizer;

use Palmtree\Csv\Normalizer\ArrayNormalizer;
use Palmtree\Csv\Normalizer\NumberNormalizer;
use Palmtree\Csv\Normalizer\StringNormalizer;
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

    public function testEmptyString(): void
    {
        $normalizer = new ArrayNormalizer();

        $value = $normalizer->normalize('');

        $this->assertSame([''], $value);
    }

    public function testSingleValue(): void
    {
        $normalizer = new ArrayNormalizer();

        $value = $normalizer->normalize('single');

        $this->assertSame(['single'], $value);
    }

    public function testWhitespaceTrimming(): void
    {
        $normalizer = new ArrayNormalizer(new StringNormalizer());

        $value = $normalizer->normalize(' a , b , c ');

        $this->assertSame(['a', 'b', 'c'], $value);
    }

    public function testWithNumberNormalizer(): void
    {
        $normalizer = new ArrayNormalizer(new NumberNormalizer());

        $value = $normalizer->normalize('1.5, 2.5, 3.5');

        $this->assertSame([1.5, 2.5, 3.5], $value);
    }

    public function testWithStringNormalizer(): void
    {
        $normalizer = new ArrayNormalizer(new StringNormalizer());

        $value = $normalizer->normalize(' a , b , c ');

        $this->assertSame(['a', 'b', 'c'], $value);
    }

    public function testPipeDelimiter(): void
    {
        $normalizer = new ArrayNormalizer();
        $normalizer->delimiter('|');

        $value = $normalizer->normalize('apple|banana|cherry');

        $this->assertSame(['apple', 'banana', 'cherry'], $value);
    }

    public function testSemicolonDelimiter(): void
    {
        $normalizer = new ArrayNormalizer();
        $normalizer->delimiter(';');

        $value = $normalizer->normalize('one;two;three');

        $this->assertSame(['one', 'two', 'three'], $value);
    }

    public function testTabDelimiter(): void
    {
        $normalizer = new ArrayNormalizer();
        $normalizer->delimiter("\t");

        $value = $normalizer->normalize("a\tb\tc");

        $this->assertSame(['a', 'b', 'c'], $value);
    }

    public function testFluentInterface(): void
    {
        $normalizer = new ArrayNormalizer();
        $result = $normalizer->delimiter('|');

        $this->assertSame($normalizer, $result);
    }

    public function testChangingDelimiterMultipleTimes(): void
    {
        $normalizer = new ArrayNormalizer();

        // First with comma
        $value1 = $normalizer->normalize('a,b,c');
        $this->assertSame(['a', 'b', 'c'], $value1);

        // Change to pipe
        $normalizer->delimiter('|');
        $value2 = $normalizer->normalize('a|b|c');
        $this->assertSame(['a', 'b', 'c'], $value2);

        // Change back to comma (but value still has pipes)
        $normalizer->delimiter(',');
        $value3 = $normalizer->normalize('a|b|c');
        $this->assertSame(['a|b|c'], $value3);
    }

    public function testNumericStringArray(): void
    {
        $normalizer = new ArrayNormalizer(new NumberNormalizer());

        $value = $normalizer->normalize('0001,0002,0003');

        $this->assertSame([1, 2, 3], $value);
    }

    public function testNonNumericWithNumberNormalizer(): void
    {
        $normalizer = new ArrayNormalizer(new NumberNormalizer());

        $value = $normalizer->normalize('abc,def,123');

        $this->assertSame([0, 0, 123], $value);
    }

    public function testComplexDelimiterHandling(): void
    {
        $normalizer = new ArrayNormalizer();
        $normalizer->delimiter('|');

        $value = $normalizer->normalize('a,b,c|d,e,f');

        $this->assertSame(['a,b,c', 'd,e,f'], $value);
    }

    public function testManyElements(): void
    {
        $normalizer = new ArrayNormalizer();

        $value = $normalizer->normalize('1,2,3,4,5,6,7,8,9,10');

        $this->assertSame(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'], $value);
    }
}
