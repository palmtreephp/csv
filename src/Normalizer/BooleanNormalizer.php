<?php

namespace Palmtree\Csv\Normalizer;

/**
 * BooleanNormalizer formats a CSV cell as a boolean.
 */
class BooleanNormalizer extends AbstractNormalizer
{
    /**
     * @var array Default truthy/falsey pairs.
     */
    public static $defaultPairs = [
        'true'    => 'false',
        '1'       => '0',
        'on'      => 'off',
        'yes'     => 'no',
        'enabled' => 'disabled',
    ];

    /** @var array */
    private $values = [];
    /** @var bool */
    private $nullable = false;
    /** @var bool */
    private $caseSensitive = false;

    public function __construct(NormalizerInterface $normalizer = null)
    {
        $this->setPairs(self::$defaultPairs);

        parent::__construct($normalizer);
    }

    public function setPairs(array $pairs): self
    {
        $this->values = [];

        foreach ($pairs as $truthy => $falsey) {
            $this->addPair($truthy, $falsey);
        }

        return $this;
    }

    public function addPair(string $truthy, string $falsey): void
    {
        if (!$this->caseSensitive) {
            $truthy = \strtolower($truthy);
            $falsey = \strtolower($falsey);
        }

        $this->values[$truthy] = true;
        $this->values[$falsey] = false;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return bool|null
     */
    protected function getNormalizedValue(string $value)
    {
        $value = \trim($value);

        if (!$this->caseSensitive) {
            $value = \strtolower($value);
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
    public function setCaseSensitive(bool $caseSensitive): self
    {
        $this->caseSensitive = $caseSensitive;

        return $this;
    }

    public function isCaseSensitive(): bool
    {
        return $this->caseSensitive;
    }

    /**
     * Sets whether the returned value can be null. Defaults to false, meaning any value present that is
     * not found in the truthy values will return false.
     */
    public function setNullable(bool $nullable): self
    {
        $this->nullable = $nullable;

        return $this;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }
}
