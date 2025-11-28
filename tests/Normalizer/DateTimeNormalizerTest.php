<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test\Normalizer;

use Palmtree\Csv\Normalizer\DateTimeNormalizer;
use PHPUnit\Framework\TestCase;

class DateTimeNormalizerTest extends TestCase
{
    public function testDateTimeNormalizerDefaultFormat(): void
    {
        $normalizer = new DateTimeNormalizer();

        $result = $normalizer->normalize('2024-12-25');

        $this->assertInstanceOf(\DateTime::class, $result);
        $this->assertSame('2024-12-25', $result->format('Y-m-d'));
    }

    public function testDateTimeNormalizerCustomFormat(): void
    {
        $normalizer = new DateTimeNormalizer();
        $normalizer->format('m/d/Y');

        $result = $normalizer->normalize('12/25/2024');

        $this->assertInstanceOf(\DateTime::class, $result);
        $this->assertSame('2024-12-25', $result->format('Y-m-d'));
    }

    public function testDateTimeNormalizerWithTime(): void
    {
        $normalizer = new DateTimeNormalizer();
        $normalizer->format('Y-m-d H:i:s');

        $result = $normalizer->normalize('2024-12-25 14:30:45');

        $this->assertInstanceOf(\DateTime::class, $result);
        $this->assertSame('14:30:45', $result->format('H:i:s'));
    }

    public function testDateTimeNormalizerInvalidFormatReturnsNull(): void
    {
        $normalizer = new DateTimeNormalizer();
        $normalizer->format('Y-m-d');

        $result = $normalizer->normalize('invalid-date');

        $this->assertNull($result);
    }

    public function testDateTimeNormalizerFormatReturnsInstance(): void
    {
        $normalizer = new DateTimeNormalizer();

        $result = $normalizer->format('d/m/Y');

        $this->assertSame($normalizer, $result);
    }

    public function testDateTimeNormalizerWithDifferentFormats(): void
    {
        $testCases = [
            ['d/m/Y', '25/12/2024', '2024-12-25'],
            ['m-d-Y', '12-25-2024', '2024-12-25'],
            ['Y/m/d', '2024/12/25', '2024-12-25'],
        ];

        foreach ($testCases as [$format, $input, $expected]) {
            $normalizer = new DateTimeNormalizer();
            $normalizer->format($format);

            $result = $normalizer->normalize($input);

            $this->assertInstanceOf(\DateTime::class, $result);
            $this->assertSame($expected, $result->format('Y-m-d'));
        }
    }
}
