<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test\Cell;

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Normalizer\ArrayNormalizer;
use Palmtree\Csv\Normalizer\BooleanNormalizer;
use Palmtree\Csv\Normalizer\NullNormalizer;
use Palmtree\Csv\Normalizer\NumberNormalizer;
use Palmtree\Csv\Normalizer\StringNormalizer;
use PHPUnit\Framework\TestCase;

class CellTest extends TestCase
{
    public function testCellFormatting(): void
    {
        $cell = new Cell('1', new NumberNormalizer());
        $this->assertSame(1, $cell->getValue());
        $this->assertSame('1', $cell->getRawValue());

        $cell = new Cell('true', new BooleanNormalizer());
        $this->assertTrue($cell->getValue());
        $this->assertSame('true', $cell->getRawValue());
    }

    public function testGetValueWithNumberNormalizer(): void
    {
        $cell = new Cell('123.45', new NumberNormalizer());
        $this->assertSame(123.45, $cell->getValue());
    }

    public function testGetValueWithStringNormalizer(): void
    {
        $cell = new Cell('  hello  ', new StringNormalizer());
        $this->assertSame('hello', $cell->getValue());
    }

    public function testGetValueWithBooleanNormalizer(): void
    {
        $cell = new Cell('yes', new BooleanNormalizer());
        $this->assertTrue($cell->getValue());

        $cell = new Cell('no', new BooleanNormalizer());
        $this->assertFalse($cell->getValue());
    }

    public function testGetValueWithNullNormalizer(): void
    {
        $cell = new Cell('anything', new NullNormalizer());
        $this->assertSame('anything', $cell->getValue());
    }

    public function testGetValueWithArrayNormalizer(): void
    {
        $cell = new Cell('a,b,c', new ArrayNormalizer());
        $value = $cell->getValue();

        $this->assertIsArray($value);
        $this->assertSame(['a', 'b', 'c'], $value);
    }

    public function testGetRawValueUnchanged(): void
    {
        $originalValue = '  123  ';
        $cell = new Cell($originalValue, new NumberNormalizer());

        $this->assertSame($originalValue, $cell->getRawValue());
    }

    public function testGetNormalizer(): void
    {
        $normalizer = new BooleanNormalizer();
        $cell = new Cell('true', $normalizer);

        $this->assertSame($normalizer, $cell->getNormalizer());
    }

    public function testToStringWithNumeric(): void
    {
        $cell = new Cell('42', new NumberNormalizer());
        $this->assertSame('42', (string)$cell);
    }

    public function testToStringWithBooleanTrue(): void
    {
        $cell = new Cell('yes', new BooleanNormalizer());
        $this->assertSame('1', (string)$cell);
    }

    public function testToStringWithBooleanFalse(): void
    {
        $cell = new Cell('no', new BooleanNormalizer());
        $this->assertSame('', (string)$cell);
    }

    public function testToStringWithString(): void
    {
        $cell = new Cell('hello', new StringNormalizer());
        $this->assertSame('hello', (string)$cell);
    }

    public function testToStringWithArray(): void
    {
        $cell = new Cell('a,b,c', new ArrayNormalizer());
        // Converting array to string will trigger exception, caught in __toString
        $this->assertSame('', (string)$cell);
    }

    public function testMultipleGetValueCalls(): void
    {
        $cell = new Cell('123', new NumberNormalizer());

        $value1 = $cell->getValue();
        $value2 = $cell->getValue();

        $this->assertSame($value1, $value2);
    }

    public function testEmptyStringValue(): void
    {
        $cell = new Cell('', new StringNormalizer());
        $this->assertSame('', $cell->getValue());
        $this->assertSame('', $cell->getRawValue());
    }

    public function testWhitespaceOnlyValue(): void
    {
        $cell = new Cell('   ', new StringNormalizer());
        $this->assertSame('', $cell->getValue());
        $this->assertSame('   ', $cell->getRawValue());
    }

    public function testStringableInterface(): void
    {
        $cell = new Cell('test', new StringNormalizer());

        // Cell implements Stringable interface
        $this->assertSame('test', (string)$cell);
    }

    public function testSpecialCharacters(): void
    {
        $specialChars = '!@#$%^&*()';
        $cell = new Cell($specialChars, new StringNormalizer());

        $this->assertSame($specialChars, $cell->getRawValue());
    }

    public function testNewlineCharacters(): void
    {
        $value = "line1\nline2";
        $cell = new Cell($value, new StringNormalizer());

        $this->assertSame($value, $cell->getRawValue());
    }

    public function testCellsWithDifferentNormalizers(): void
    {
        $cell1 = new Cell('123', new NumberNormalizer());
        $cell2 = new Cell('123', new StringNormalizer());

        $this->assertSame(123, $cell1->getValue());
        $this->assertSame('123', $cell2->getValue());
        $this->assertSame($cell1->getRawValue(), $cell2->getRawValue());
    }
}
