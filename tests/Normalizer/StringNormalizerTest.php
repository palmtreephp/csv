<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test\Normalizer;

use Palmtree\Csv\Normalizer\StringNormalizer;
use PHPUnit\Framework\TestCase;

class StringNormalizerTest extends TestCase
{
    public function testNormalizerTrimsStrings(): void
    {
        $normalizer = new StringNormalizer();
        $normalizer->trim(true);

        $value = $normalizer->normalize(' foo bar baz ');

        $this->assertSame('foo bar baz', $value);
    }

    public function testNormalizerDoesNotTrimStrings(): void
    {
        $normalizer = new StringNormalizer();
        $normalizer->trim(false);

        $value = $normalizer->normalize(' foo bar baz ');

        $this->assertSame(' foo bar baz ', $value);
    }
}
