<?php

namespace Palmtree\Csv\Formatter;

/**
 * BooleanFormatter formats a CSV cell as a boolean
 */
class BooleanFormatter extends AbstractFormatter
{
    /**
     * @var array $defaultPairs Default truthy/falsey pairs.
     */
    public static $defaultPairs = [
        'true'    => 'false',
        '1'       => '0',
        'on'      => 'off',
        'yes'     => 'no',
        'enabled' => 'disabled',
    ];

    /** @var array */
    protected $values = [];
    /** @var bool */
    protected $nullable;
    /** @var bool */
    protected $caseSensitive;

    /**
     * BooleanFormatter constructor.
     *
     * @param null|FormatterInterface $formatter
     * @param bool                    $nullable
     * @param bool                    $caseSensitive
     * @param array|null              $pairs
     */
    public function __construct($formatter = null, $nullable = false, $caseSensitive = false, $pairs = null)
    {
        if (!is_array($pairs)) {
            $pairs = static::$defaultPairs;
        }

        $this->setNullable($nullable)
             ->setCaseSensitive($caseSensitive)
             ->setPairs($pairs);

        parent::__construct($formatter);
    }

    /**
     * @param array $pairs
     *
     * @return BooleanFormatter
     */
    public function setPairs(array $pairs)
    {
        $this->values = [];

        foreach ($pairs as $truthy => $falsey) {
            $this->addPair($truthy, $falsey);
        }

        return $this;
    }

    /**
     * @param string $truthy
     * @param string $falsey
     */
    public function addPair($truthy, $falsey)
    {
        if (!$this->isCaseSensitive()) {
            $truthy = strtolower($truthy);
            $falsey = strtolower($falsey);
        }

        $this->values[$truthy] = true;
        $this->values[$falsey] = false;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param $value
     *
     * @return bool|null
     */
    protected function getFormattedValue($value)
    {
        $value = trim($value);

        if (!$this->isCaseSensitive()) {
            $value = strtolower($value);
        }

        if (isset($this->values[$value])) {
            return $this->values[$value];
        }

        return $this->isNullable() ? null : false;
    }

    /**
     * @param bool $caseSensitive
     *
     * @return BooleanFormatter
     */
    public function setCaseSensitive($caseSensitive)
    {
        $this->caseSensitive = $caseSensitive;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCaseSensitive()
    {
        return $this->caseSensitive;
    }

    /**
     * @param bool $nullable
     *
     * @return BooleanFormatter
     */
    public function setNullable($nullable)
    {
        $this->nullable = (bool)$nullable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNullable()
    {
        return $this->nullable;
    }
}
