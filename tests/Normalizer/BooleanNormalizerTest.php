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
        $normalizer->pairs($pairs);

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
        $normalizer->nullable(true);
        $normalizer->pairs($pairs);

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

    public function testDefaultPairsInitialized(): void
    {
        $normalizer = new BooleanNormalizer();

        // Test all default truthy values
        $this->assertTrue($normalizer->normalize('true'));
        $this->assertTrue($normalizer->normalize('1'));
        $this->assertTrue($normalizer->normalize('on'));
        $this->assertTrue($normalizer->normalize('yes'));
        $this->assertTrue($normalizer->normalize('enabled'));

        // Test all default falsey values
        $this->assertFalse($normalizer->normalize('false'));
        $this->assertFalse($normalizer->normalize('0'));
        $this->assertFalse($normalizer->normalize('off'));
        $this->assertFalse($normalizer->normalize('no'));
        $this->assertFalse($normalizer->normalize('disabled'));
    }

    public function testCaseInsensitiveByDefault(): void
    {
        $normalizer = new BooleanNormalizer();

        // Mixed case should still match
        $this->assertTrue($normalizer->normalize('True'));
        $this->assertTrue($normalizer->normalize('TRUE'));
        $this->assertTrue($normalizer->normalize('TrUe'));
        $this->assertTrue($normalizer->normalize('Yes'));
        $this->assertTrue($normalizer->normalize('YES'));

        $this->assertFalse($normalizer->normalize('False'));
        $this->assertFalse($normalizer->normalize('FALSE'));
        $this->assertFalse($normalizer->normalize('No'));
        $this->assertFalse($normalizer->normalize('NO'));
    }

    public function testCaseSensitiveMode(): void
    {
        $normalizer = new BooleanNormalizer();
        $normalizer->caseSensitive(true);

        // Exact case should match
        $this->assertTrue($normalizer->normalize('true'));
        $this->assertFalse($normalizer->normalize('false'));

        // Different case should not match
        $this->assertFalse($normalizer->normalize('True'));
        $this->assertFalse($normalizer->normalize('TRUE'));
        $this->assertFalse($normalizer->normalize('False'));
    }

    public function testWhitespaceTrimming(): void
    {
        $normalizer = new BooleanNormalizer();

        // Leading and trailing whitespace should be trimmed
        $this->assertTrue($normalizer->normalize('  true  '));
        $this->assertTrue($normalizer->normalize("\ttrue\t"));
        $this->assertTrue($normalizer->normalize("\ntrue\n"));
        $this->assertFalse($normalizer->normalize('  false  '));
        $this->assertFalse($normalizer->normalize('  0  '));
    }

    public function testAddPairMethod(): void
    {
        $normalizer = new BooleanNormalizer();
        $normalizer->pairs([]); // Clear default pairs
        $normalizer->addPair('y', 'n');

        $this->assertTrue($normalizer->normalize('y'));
        $this->assertFalse($normalizer->normalize('n'));
    }

    public function testAddPairCaseInsensitive(): void
    {
        $normalizer = new BooleanNormalizer();
        $normalizer->pairs([]);
        $normalizer->addPair('YES', 'NO');

        // Should match regardless of case
        $this->assertTrue($normalizer->normalize('yes'));
        $this->assertTrue($normalizer->normalize('YES'));
        $this->assertFalse($normalizer->normalize('no'));
        $this->assertFalse($normalizer->normalize('NO'));
    }

    public function testAddPairCaseSensitive(): void
    {
        $normalizer = new BooleanNormalizer();
        $normalizer->caseSensitive(true);
        $normalizer->pairs([]);
        $normalizer->addPair('YES', 'NO');

        // Should match only exact case
        $this->assertTrue($normalizer->normalize('YES'));
        $this->assertFalse($normalizer->normalize('yes'));
        $this->assertFalse($normalizer->normalize('NO'));
    }

    public function testNullableFalseReturnsfalseForUnknown(): void
    {
        $normalizer = new BooleanNormalizer();
        $normalizer->nullable(false);

        $this->assertFalse($normalizer->normalize('unknown'));
        $this->assertFalse($normalizer->normalize('random'));
        $this->assertFalse($normalizer->normalize(''));
    }

    public function testNullableTrueReturnsNullForUnknown(): void
    {
        $normalizer = new BooleanNormalizer();
        $normalizer->nullable(true);

        $this->assertNull($normalizer->normalize('unknown'));
        $this->assertNull($normalizer->normalize('random'));
        $this->assertNull($normalizer->normalize(''));
    }

    public function testFluentInterface(): void
    {
        $normalizer = new BooleanNormalizer();
        $result = $normalizer
            ->pairs(['y' => 'n'])
            ->caseSensitive(true)
            ->nullable(true);

        $this->assertSame($normalizer, $result);
        $this->assertTrue($normalizer->normalize('y'));
        $this->assertFalse($normalizer->normalize('n'));
        $this->assertNull($normalizer->normalize('unknown'));
    }

    public function testPairsMethodResetsPreviousPairs(): void
    {
        $normalizer = new BooleanNormalizer();
        // Initially has default pairs
        $this->assertTrue($normalizer->normalize('true'));

        // Call pairs to set new pairs
        $normalizer->pairs(['y' => 'n']);

        // Now default pairs should no longer work
        $this->assertFalse($normalizer->normalize('true')); // Returns false (not found, nullable=false)
        $this->assertTrue($normalizer->normalize('y'));
        $this->assertFalse($normalizer->normalize('n'));
    }

    public function testMultipleAddPairCalls(): void
    {
        $normalizer = new BooleanNormalizer();
        $normalizer->pairs([]);

        // Add multiple pairs individually
        $normalizer->addPair('true', 'false');
        $normalizer->addPair('yes', 'no');
        $normalizer->addPair('1', '0');

        $this->assertTrue($normalizer->normalize('true'));
        $this->assertTrue($normalizer->normalize('yes'));
        $this->assertTrue($normalizer->normalize('1'));
        $this->assertFalse($normalizer->normalize('false'));
        $this->assertFalse($normalizer->normalize('no'));
        $this->assertFalse($normalizer->normalize('0'));
    }
}
