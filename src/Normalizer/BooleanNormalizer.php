<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

/**
 * BooleanNormalizer converts a string to boolean true or false, or null.
 */
class BooleanNormalizer extends AbstractNormalizer
{
    /**
     * @var array<array-key, string> Default truthy/falsey pairs.
     */
    public static array $defaultPairs = [
        'true' => 'false',
        '1' => '0',
        'on' => 'off',
        'yes' => 'no',
        'enabled' => 'disabled',
    ];

    /** @var array<array-key, bool> */
    private array $values = [];
    private bool $nullable = false;
    private bool $caseSensitive = false;

    public function __construct(?NormalizerInterface $normalizer = null)
    {
        $this->pairs(self::$defaultPairs);

        parent::__construct($normalizer);
    }

    /**
     * @param array<array-key, string> $pairs Truthy values as keys, falsey values as values.
     */
    public function pairs(array $pairs): self
    {
        $this->values = [];

        foreach ($pairs as $truthy => $falsey) {
            $this->addPair((string)$truthy, $falsey);
        }

        return $this;
    }

    public function addPair(string $truthy, string $falsey): void
    {
        $this->values[$truthy] = true;
        $this->values[$falsey] = false;
    }

    protected function getNormalizedValue(string $value): ?bool
    {
        $value = trim($value);
        $values = $this->values;

        if (!$this->caseSensitive) {
            $value = strtolower($value);
            $values = array_change_key_case($values);
        }

        return $values[$value] ?? ($this->nullable ? null : false);
    }

    /**
     * Sets whether a case-sensitive comparison should be made. If this is true, 'Enabled' will not match 'enabled'
     * and will return false if it is found (or null if isNullable is true). Defaults to false.
     */
    public function caseSensitive(bool $caseSensitive): self
    {
        $this->caseSensitive = $caseSensitive;

        return $this;
    }

    /**
     * Sets whether the returned value can be null. Defaults to false, meaning any value present that is
     * not found in the truthy values will return false.
     */
    public function nullable(bool $nullable): self
    {
        $this->nullable = $nullable;

        return $this;
    }
}
