<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test\Normalizer;

use Palmtree\Csv\Normalizer\BooleanNormalizer;
use PHPUnit\Framework\TestCase;

class BooleanNormalizerTest extends TestCase
{
    /**
     * @dataProvider configProvider
     */
    public function testTruthyValues(array $pairs): void
    {
        $normalizer = new BooleanNormalizer();
        $normalizer->setPairs($pairs);

        $this->assertTrue($normalizer->normalize('true'));
        $this->assertTrue($normalizer->normalize('enabled'));
        $this->assertTrue($normalizer->normalize('1'));
        $this->assertTrue($normalizer->normalize('on'));
        $this->assertFalse($normalizer->normalize('foo'));
    }

    /**
     * @dataProvider configProvider
     */
    public function testFalseyValues(array $pairs): void
    {
        $normalizer = new BooleanNormalizer();
        $normalizer->setNullable(true);
        $normalizer->setPairs($pairs);

        $this->assertFalse($normalizer->normalize('false'));
        $this->assertFalse($normalizer->normalize('disabled'));
        $this->assertFalse($normalizer->normalize('0'));
        $this->assertFalse($normalizer->normalize('off'));

        $this->assertNull($normalizer->normalize('bar'));
    }

    public function configProvider(): array
    {
        return [
            [
                [
                    'true' => 'false',
                    'enabled' => 'disabled',
                    '1' => '0',
                    'on' => 'off',
                ],
            ],
        ];
    }
}
