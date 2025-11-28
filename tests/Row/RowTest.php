<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test\Row;

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Normalizer\BooleanNormalizer;
use Palmtree\Csv\Normalizer\NumberNormalizer;
use Palmtree\Csv\Reader;
use Palmtree\Csv\Row\Row;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    public function testRowArrayAccess(): void
    {
        $reader = new Reader('php://memory');

        $row = new Row(['foo', 'bar'], $reader);

        $this->assertSame('bar', $row[1]);

        $row[2] = 'baz';

        $cells = $row->getCells();

        $this->assertSame('baz', $cells[2]->getValue());

        unset($row[2]);

        $cells = $row->getCells();

        $this->assertArrayNotHasKey(2, $cells);
    }

    public function testRowIsCountable(): void
    {
        $reader = new Reader('php://memory');

        $row = new Row(['foo', 'bar'], $reader);

        $this->assertCount(2, $row);
    }

    public function testRowIsTraversable(): void
    {
        $reader = new Reader('php://memory');

        $row = new Row(['foo', 'bar'], $reader);

        foreach ($row as $i => $cell) {
            $this->assertSame($i ? 'bar' : 'foo', $cell->getValue());
        }
    }

    public function testCanGetReader(): void
    {
        $reader = new Reader('php://memory');

        $row = new Row(['foo', 'bar'], $reader);
        $this->assertSame($reader, $row->getReader());
    }

    public function testGetCellsReturnsCellObjects(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row(['foo', 'bar'], $reader);

        $cells = $row->getCells();

        // $cells is array of Cell objects from getCells() return type
        $this->assertCount(2, $cells);
        $this->assertSame('foo', $cells[0]->getValue());
        $this->assertSame('bar', $cells[1]->getValue());
    }

    public function testAddCell(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row([], $reader);

        $row->addCell('name', 'John');

        $cells = $row->getCells();
        $this->assertArrayHasKey('name', $cells);
        $this->assertSame('John', $cells['name']->getValue());
    }

    public function testAddCells(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row([], $reader);

        $row->addCells(['name' => 'John', 'age' => '30']);

        $cells = $row->getCells();
        $this->assertCount(2, $cells);
        $this->assertSame('John', $cells['name']->getValue());
        $this->assertSame('30', $cells['age']->getValue());
    }

    public function testToArray(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row(['foo', 'bar', 'baz'], $reader);

        $array = $row->toArray();

        $this->assertSame(['foo', 'bar', 'baz'], $array);
    }

    public function testOffsetExistsWithExistingKey(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row(['foo', 'bar'], $reader);

        $this->assertTrue(isset($row[0]));
        $this->assertTrue(isset($row[1]));
    }

    public function testOffsetExistsWithNonExistingKey(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row(['foo', 'bar'], $reader);

        $this->assertFalse(isset($row[5]));
    }

    public function testOffsetGetReturnsNormalizedValue(): void
    {
        $reader = new Reader('php://memory');
        $reader->addNormalizer('id', new NumberNormalizer());
        $row = new Row(['id' => '123'], $reader);

        $this->assertSame(123, $row['id']);
    }

    public function testOffsetSetCreatesNewCell(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row([], $reader);

        $row[0] = 'value';

        $this->assertSame('value', $row[0]);
    }

    public function testOffsetUnsetRemovesCell(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row(['foo', 'bar'], $reader);

        unset($row[0]);

        $this->assertFalse(isset($row[0]));
        $this->assertCount(1, $row);
    }

    public function testCountReturnsCellCount(): void
    {
        $reader = new Reader('php://memory');

        $row = new Row(['a', 'b', 'c'], $reader);
        $this->assertCount(3, $row);

        unset($row[0]);
        $this->assertCount(2, $row);
    }

    public function testEmptyRow(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row([], $reader);

        $this->assertCount(0, $row);
        $this->assertSame([], $row->toArray());
    }

    public function testGetIteratorReturnsArrayIterator(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row(['foo', 'bar'], $reader);

        $iterator = $row->getIterator();

        $this->assertCount(2, $iterator);
    }

    public function testIterationPreservesOrder(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row(['first', 'second', 'third'], $reader);

        $values = [];
        foreach ($row as $cell) {
            $values[] = $cell->getValue();
        }

        $this->assertSame(['first', 'second', 'third'], $values);
    }

    public function testRowWithNormalizers(): void
    {
        $reader = new Reader('php://memory');
        $reader->addNormalizer('count', new NumberNormalizer());
        $reader->addNormalizer('active', new BooleanNormalizer());

        $row = new Row(['count' => '42', 'active' => 'yes'], $reader);

        $this->assertSame(42, $row['count']);
        $this->assertTrue($row['active']);
    }

    public function testAddCellsWithNumericKeys(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row([], $reader);

        $row->addCell(0, 'value0');
        $row->addCell(1, 'value1');

        $cells = $row->getCells();
        $this->assertCount(2, $cells);
    }

    public function testAddCellsWithStringKeys(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row([], $reader);

        $row->addCell('name', 'John');
        $row->addCell('email', 'john@example.com');

        $cells = $row->getCells();
        $this->assertCount(2, $cells);
    }

    public function testReplacingExistingCell(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row(['value1'], $reader);

        $originalValue = $row[0];
        $row[0] = 'value2';
        $newValue = $row[0];

        $this->assertNotSame($originalValue, $newValue);
    }

    public function testRowWithMixedKeyTypes(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row([0 => 'numeric', 'string' => 'key'], $reader);

        $this->assertSame('numeric', $row[0]);
        $this->assertSame('key', $row['string']);
    }

    public function testMultipleIterationsOverSameRow(): void
    {
        $reader = new Reader('php://memory');
        $row = new Row(['a', 'b'], $reader);

        // First iteration
        $count1 = 0;
        foreach ($row as $cell) {
            ++$count1;
        }

        // Second iteration
        $count2 = 0;
        foreach ($row as $cell) {
            ++$count2;
        }

        $this->assertSame($count1, $count2);
        $this->assertSame(2, $count1);
    }
}
