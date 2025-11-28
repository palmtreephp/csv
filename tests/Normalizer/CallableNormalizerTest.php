<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test\Normalizer;

use Palmtree\Csv\Normalizer\CallableNormalizer;
use Palmtree\Csv\Normalizer\NullNormalizer;
use PHPUnit\Framework\TestCase;

class CallableNormalizerTest extends TestCase
{
    public function testCallableNormalizerWithSimpleCallback(): void
    {
        $normalizer = new CallableNormalizer(fn ($value) => strtoupper($value));

        $result = $normalizer->normalize('hello');

        $this->assertSame('HELLO', $result);
    }

    public function testCallableNormalizerWithChainedNormalizers(): void
    {
        $innerNormalizer = new NullNormalizer();
        $normalizer = new CallableNormalizer(fn ($value) => trim($value), $innerNormalizer);

        $result = $normalizer->normalize('  hello  ');

        $this->assertSame('hello', $result);
    }

    public function testCallableNormalizerWithCustomNormalizer(): void
    {
        $mockNormalizer = new class extends \Palmtree\Csv\Normalizer\AbstractNormalizer {
            protected function getNormalizedValue(string $value): string
            {
                return 'prefix_' . $value;
            }
        };

        $normalizer = new CallableNormalizer(fn ($value) => strtoupper($value), $mockNormalizer);

        $result = $normalizer->normalize('test');

        $this->assertSame('PREFIX_TEST', $result);
    }

    public function testCallableNormalizerPassesNormalizerInstance(): void
    {
        $callbackNormalizer = null;
        $normalizer = new CallableNormalizer(function ($value, $norm) use (&$callbackNormalizer) {
            $callbackNormalizer = $norm;

            return $value;
        });

        $normalizer->normalize('test');

        $this->assertInstanceOf(CallableNormalizer::class, $callbackNormalizer);
    }

    public function testCallableNormalizerWithNumericValue(): void
    {
        $normalizer = new CallableNormalizer(fn ($value) => $value * 2);

        $result = $normalizer->normalize('5');

        $this->assertSame(10, $result);
    }
}
