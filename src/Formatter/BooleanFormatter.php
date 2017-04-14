<?php

namespace Palmtree\Csv\Formatter;

/**
 * BooleanFormatter formats a CSV cell as a boolean
 */
class BooleanFormatter extends AbstractFormatter
{
    protected $nullable = false;

    protected $binaries = [
        '1'       => '0',
        'true'    => 'false',
        'on'      => 'off',
        'yes'     => 'no',
        'enabled' => 'disabled',
    ];

    /**
     * BooleanFormatter constructor.
     *
     * @param null|FormatterInterface $formatter
     * @param bool                    $nullable
     * @param null                    $binaries
     */
    public function __construct($formatter = null, $nullable = false, $binaries = null)
    {
        $this->setNullable($nullable);

        if (is_array($binaries)) {
            $this->setBinaries($binaries);
        }

        parent::__construct($formatter);
    }

    /**
     * @return bool
     */
    public function isNullable()
    {
        return $this->nullable;
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
     * @param array $binaries
     *
     * @return BooleanFormatter
     */
    public function setBinaries(array $binaries)
    {
        $this->binaries = $binaries;

        return $this;
    }

    /**
     * @param string $truthy
     * @param string $falsey
     */
    public function addBinary($truthy, $falsey)
    {
        $this->binaries[$truthy] = $falsey;
    }

    /**
     * @param $key
     */
    public function removeBinary($key)
    {
        unset($this->binaries[$key]);
    }

    /**
     * @return array
     */
    public function getBinaries()
    {
        return $this->binaries;
    }

    protected function getFormattedValue($value)
    {
        $value = trim($value);

        foreach ($this->getBinaries() as $truthy => $falsey) {
            if (strcasecmp(trim($truthy), $value) === 0) {
                return true;
            }

            if (strcasecmp(trim($falsey), $value) === 0) {
                return false;
            }
        }

        return $this->isNullable() ? null : false;
    }
}
