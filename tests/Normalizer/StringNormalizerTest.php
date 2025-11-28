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

    public function testDefaultBehaviorTrims(): void
    {
        $normalizer = new StringNormalizer();

        $value = $normalizer->normalize('  hello  ');

        $this->assertSame('hello', $value);
    }

    public function testTrimWithTabs(): void
    {
        $normalizer = new StringNormalizer();
        $normalizer->trim(true);

        $value = $normalizer->normalize("\thello\t");

        $this->assertSame('hello', $value);
    }

    public function testTrimWithNewlines(): void
    {
        $normalizer = new StringNormalizer();
        $normalizer->trim(true);

        $value = $normalizer->normalize("\nhello\n");

        $this->assertSame('hello', $value);
    }

    public function testTrimWithCarriageReturns(): void
    {
        $normalizer = new StringNormalizer();
        $normalizer->trim(true);

        $value = $normalizer->normalize("\rhello\r");

        $this->assertSame('hello', $value);
    }

    public function testTrimWithMixedWhitespace(): void
    {
        $normalizer = new StringNormalizer();
        $normalizer->trim(true);

        $value = $normalizer->normalize(" \t\n hello \n\t ");

        $this->assertSame('hello', $value);
    }

    public function testCustomTrimCharacters(): void
    {
        $normalizer = new StringNormalizer();
        $normalizer->trimChars(['*', '-']);

        $value = $normalizer->normalize('---hello***');

        $this->assertSame('hello', $value);
    }

    public function testCustomTrimCharsDontTrimWhitespace(): void
    {
        $normalizer = new StringNormalizer();
        $normalizer->trimChars(['*']);

        $value = $normalizer->normalize('* hello *');

        $this->assertSame(' hello ', $value);
    }

    public function testAddTrimChar(): void
    {
        $normalizer = new StringNormalizer();
        $normalizer->addTrimChar('x');

        $value = $normalizer->normalize('xxhellox ');

        // Default trim chars are still applied, plus 'x'
        $this->assertSame('hello', $value);
    }

    public function testAddingMultipleTrimChars(): void
    {
        $normalizer = new StringNormalizer();
        $normalizer->addTrimChar('*');
        $normalizer->addTrimChar('-');

        $value = $normalizer->normalize('**--hello--** ');

        $this->assertSame('hello', $value);
    }

    public function testEmptyString(): void
    {
        $normalizer = new StringNormalizer();

        $value = $normalizer->normalize('');

        $this->assertSame('', $value);
    }

    public function testStringWithOnlyWhitespace(): void
    {
        $normalizer = new StringNormalizer();
        $normalizer->trim(true);

        $value = $normalizer->normalize('   ');

        $this->assertSame('', $value);
    }

    public function testInternalWhitespacePreserved(): void
    {
        $normalizer = new StringNormalizer();
        $normalizer->trim(true);

        $value = $normalizer->normalize('  hello   world  ');

        $this->assertSame('hello   world', $value);
    }

    public function testFluentInterfaceTrim(): void
    {
        $normalizer = new StringNormalizer();
        $result = $normalizer->trim(false);

        $this->assertSame($normalizer, $result);
    }

    public function testFluentInterfaceTrimChars(): void
    {
        $normalizer = new StringNormalizer();
        $result = $normalizer->trimChars(['*']);

        $this->assertSame($normalizer, $result);
    }

    public function testFluentInterfaceAddTrimChar(): void
    {
        $normalizer = new StringNormalizer();
        $result = $normalizer->addTrimChar('x');

        $this->assertSame($normalizer, $result);
    }

    public function testMethodChaining(): void
    {
        $normalizer = new StringNormalizer();
        $result = $normalizer
            ->trim(true)
            ->addTrimChar('!')
            ->addTrimChar('?');

        $this->assertSame($normalizer, $result);

        $value = $normalizer->normalize('!!!hello???');
        $this->assertSame('hello', $value);
    }

    public function testTogglingTrimOnAndOff(): void
    {
        $normalizer = new StringNormalizer();

        $normalizer->trim(true);
        $value1 = $normalizer->normalize('  hello  ');
        $this->assertSame('hello', $value1);

        $normalizer->trim(false);
        $value2 = $normalizer->normalize('  hello  ');
        $this->assertSame('  hello  ', $value2);

        $normalizer->trim(true);
        $value3 = $normalizer->normalize('  hello  ');
        $this->assertSame('hello', $value3);
    }

    public function testReplacingTrimCharacters(): void
    {
        $normalizer = new StringNormalizer();
        $normalizer->trimChars(['*']);

        $value1 = $normalizer->normalize('*hello*');
        $this->assertSame('hello', $value1);

        // Now replace with different characters
        $normalizer->trimChars(['-']);
        $value2 = $normalizer->normalize('-hello-');
        $this->assertSame('hello', $value2);
    }
}
