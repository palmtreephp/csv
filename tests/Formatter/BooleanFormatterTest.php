<?php

namespace Palmtree\Csv\Test\Formatter;

use Palmtree\Csv\Formatter\BooleanFormatter;
use PHPUnit\Framework\TestCase;

class BooleanFormatterTest extends TestCase
{
    /**
     * @dataProvider configProvider
     */
    public function testTruthyValues($binaries)
    {
        $formatter = new BooleanFormatter();
        $formatter->setBinaries($binaries);

        $this->assertTrue($formatter->format('true'));
        $this->assertTrue($formatter->format('enabled'));
        $this->assertTrue($formatter->format('1'));
        $this->assertTrue($formatter->format('on'));
        $this->assertFalse($formatter->format('foo'));
    }

    /**
     * @dataProvider configProvider
     */
    public function testFalseyValues($binaries)
    {
        $formatter = new BooleanFormatter();
        $formatter->setNullable(true);
        $formatter->setBinaries($binaries);

        $this->assertFalse($formatter->format('false'));
        $this->assertFalse($formatter->format('disabled'));
        $this->assertFalse($formatter->format('0'));
        $this->assertFalse($formatter->format('off'));

        $this->assertNull($formatter->format('bar'));
    }

    public function configProvider()
    {
        return [
            [
                [
                    'true'    => 'false',
                    'enabled' => 'disabled',
                    '1'       => '0',
                    'on'      => 'off',
                ],
            ],
        ];
    }
}
