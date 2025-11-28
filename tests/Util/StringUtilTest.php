<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test\Util;

use Palmtree\Csv\Util\StringUtil;
use PHPUnit\Framework\TestCase;

class StringUtilTest extends TestCase
{
    public function testHasBomWithUtf8(): void
    {
        $stringWithBom = StringUtil::BOM_UTF8 . 'Hello';
        $stringWithoutBom = 'Hello';

        $this->assertTrue(StringUtil::hasBom($stringWithBom, StringUtil::BOM_UTF8));
        $this->assertFalse(StringUtil::hasBom($stringWithoutBom, StringUtil::BOM_UTF8));
    }

    public function testHasBomWithDifferentBomTypes(): void
    {
        $utf8Bom = StringUtil::BOM_UTF8 . 'Test';
        $utf16BeBom = StringUtil::BOM_UTF16_BE . 'Test';

        $this->assertTrue(StringUtil::hasBom($utf8Bom, StringUtil::BOM_UTF8));
        $this->assertFalse(StringUtil::hasBom($utf8Bom, StringUtil::BOM_UTF16_BE));
        $this->assertTrue(StringUtil::hasBom($utf16BeBom, StringUtil::BOM_UTF16_BE));
        $this->assertFalse(StringUtil::hasBom($utf16BeBom, StringUtil::BOM_UTF8));
    }

    public function testStripBomRemovesBom(): void
    {
        $stringWithBom = StringUtil::BOM_UTF8 . 'Hello World';

        $result = StringUtil::stripBom($stringWithBom, StringUtil::BOM_UTF8);

        $this->assertSame('Hello World', $result);
        $this->assertFalse(StringUtil::hasBom($result, StringUtil::BOM_UTF8));
    }

    public function testStripBomWhenNoBomPresent(): void
    {
        $stringWithoutBom = 'Hello World';

        $result = StringUtil::stripBom($stringWithoutBom, StringUtil::BOM_UTF8);

        $this->assertSame('Hello World', $result);
    }

    public function testStripBomWithDifferentEncodings(): void
    {
        $utf16String = StringUtil::BOM_UTF16_BE . 'Test';
        $utf32String = StringUtil::BOM_UTF32_LE . 'Test';

        $result1 = StringUtil::stripBom($utf16String, StringUtil::BOM_UTF16_BE);
        $result2 = StringUtil::stripBom($utf32String, StringUtil::BOM_UTF32_LE);

        $this->assertSame('Test', $result1);
        $this->assertSame('Test', $result2);
    }

    public function testEscapeEnclosureWithSimpleArray(): void
    {
        $data = ['hello', 'world'];
        $result = StringUtil::escapeEnclosure($data, '"');

        $this->assertSame(['hello', 'world'], $result);
    }

    public function testEscapeEnclosureWithEnclosureCharacter(): void
    {
        $data = ['say "hello"', 'say "goodbye"'];
        $result = StringUtil::escapeEnclosure($data, '"');

        $this->assertSame(['say ""hello""', 'say ""goodbye""'], $result);
    }

    public function testEscapeEnclosureWithNestedArrays(): void
    {
        $data = [
            'field1' => 'value1',
            'nested' => [
                'inner' => 'value with "quotes"',
            ],
        ];
        $result = StringUtil::escapeEnclosure($data, '"');

        $this->assertSame('value1', $result['field1']);
        $this->assertSame('value with ""quotes""', $result['nested']['inner']);
    }

    public function testEscapeEnclosureWithSingleQuote(): void
    {
        $data = ["it's", "that's"];
        $result = StringUtil::escapeEnclosure($data, "'");

        $this->assertSame(["it''s", "that''s"], $result);
    }

    public function testEscapeEnclosureWithMultipleOccurrences(): void
    {
        $data = ['a"b"c"d'];
        $result = StringUtil::escapeEnclosure($data, '"');

        $this->assertSame(['a""b""c""d'], $result);
    }

    public function testEscapeEnclosurePreservesNonMatchingCharacters(): void
    {
        $data = ['hello-world', 'foo@bar'];
        $result = StringUtil::escapeEnclosure($data, '"');

        $this->assertSame(['hello-world', 'foo@bar'], $result);
    }
}
