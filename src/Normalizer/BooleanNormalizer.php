<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

/**
 * BooleanNormalizer converts a string to boolean true or false, or null.
 */
class BooleanNormalizer extends AbstractNormalizer
{
    /**
     * @var array Default truthy/falsey pairs.
     */
    public static array $defaultPairs = [
        'true' => 'false',
        '1' => '0',
        'on' => 'off',
        'yes' => 'no',
        'enabled' => 'disabled',
    ];

    private array $values = [];
    private bool $nullable = false;
    private bool $caseSensitive = false;

    public function __construct(?NormalizerInterface $normalizer = null)
    {
        $this->pairs(self::$defaultPairs);

        parent::__construct($normalizer);
    }

    public function pairs(array $pairs): static
    {
        $this->values = [];

        foreach ($pairs as $truthy => $falsey) {
            $this->addPair((string)$truthy, $falsey);
        }

        return $this;
    }

    public function addPair(string $truthy, string $falsey): void
    {
        if (!$this->caseSensitive) {
            $truthy = strtolower($truthy);
            $falsey = strtolower($falsey);
        }

        $this->values[$truthy] = true;
        $this->values[$falsey] = false;
    }

    protected function getNormalizedValue(string $value): ?bool
    {
        $value = trim($value);

        if (!$this->caseSensitive) {
            $value = strtolower($value);
        }

        if (isset($this->values[$value])) {
            return $this->values[$value];
        }

        return $this->nullable ? null : false;
    }

    /**
     * Sets whether a case-sensitive comparison should be made. If this is true, 'Enabled' will not match 'enabled'
     * and will return false if it is found (or null if isNullable is true). Defaults to false.
     */
    public function caseSensitive(bool $caseSensitive): static
    {
        $this->caseSensitive = $caseSensitive;

        return $this;
    }

    /**
     * Sets whether the returned value can be null. Defaults to false, meaning any value present that is
     * not found in the truthy values will return false.
     */
    public function nullable(bool $nullable): static
    {
        $this->nullable = $nullable;

        return $this;
    }
}
