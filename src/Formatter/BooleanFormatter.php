<?php

namespace Palmtree\Csv\Formatter;

use Palmtree\ArgParser\ArgParser;

/**
 * BooleanFormatter formats a CSV cell as a boolean
 */
class BooleanFormatter extends AbstractFormatter
{
    public static $defaultArgs = [
        'falsey' => ['false', 'off', 'no', '0', 'disabled'],
        'truthy' => ['true', 'on', 'yes', '1', 'enabled'],
    ];

    protected $args;

    public function __construct($formatter = null, $args = [])
    {
        $this->args = (new ArgParser($args))->resolveOptions(static::$defaultArgs);

        parent::__construct($formatter);

    }

    protected function getFormattedValue($value)
    {
        $trimmedValue = trim($value);

        // Boolean
        $valueLowered = mb_strtolower($trimmedValue);
        if (in_array($valueLowered, $this->args['truthy'])) {
            return true;
        }

        if (in_array($valueLowered, $this->args['falsey'])) {
            return false;
        }

        return null;
    }
}
