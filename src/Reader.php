<?php

namespace Palmtree\Csv;

use Palmtree\ArgParser\ArgParser;

/**
 * Reads a CSV file by loading each line into memory
 * one at a time.
 */
class Reader implements \Iterator, \Countable
{
    /**
     * @var array   $defaultArgs {
     * @type string $charset     Convert text to the given character set. Default false (no conversion)
     * @type bool   $headers     Whether the CSV file contains headers. Default true
     * @type string $delimiter   Cell delimiter. Default ',' (comma)
     * @type string $enclosure   Cell enclosure. Default '"' (double quote)
     * @type string $escape      Escape character. Default '\' (backslash)
     * @type string $file        Path to CSV file to parse.
     * @type bool   $normalize   Whether to normalize cell value data types. Default false
     * @type array  $falsey      Array of falsey values to convert to boolean false.
     *                            Default ['false', 'off', 'no', '0', 'disabled']
     * @type array  $truthy      Array of truthy values to convert to boolean true.
     *                            Default ['true', 'on', 'yes', '1', 'enabled']
     * }
     */
    public static $defaultArgs = [
        'file'      => '',
        'delimiter' => ',',
        'enclosure' => '"',
        'escape'    => '\\',
        'charset'   => false,
        'headers'   => true,
        'normalize' => false,
        'falsey'    => ['false', 'off', 'no', '0', 'disabled'],
        'truthy'    => ['true', 'on', 'yes', '1', 'enabled'],
    ];

    /**
     * @var array
     */
    protected $args;

    /**
     * @var resource
     */
    protected $fileHandle;

    /**
     * @var int
     */
    protected $index = 0;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var array
     */
    protected $row;

    /**
     * @var array
     */
    protected static $newLines = ["\r\n", "\r", "\n"];

    /**
     * Reader constructor.
     *
     * @param array|string $args Array of args to override $defaultArgs or
     *                           a file path to the CSV file to parse.
     */
    public function __construct($args = [])
    {
        $this->args = $this->parseArgs($args);

        $this->fileHandle = @fopen($this->args['file'], 'r');

        if (! $this->fileHandle) {
            throw new \InvalidArgumentException(
                sprintf('File %s could not be opened for reading', $this->args['file'])
            );
        }
    }

    /**
     * Reader destructor.
     */
    public function __destruct()
    {
        if ($this->fileHandle) {
            fclose($this->fileHandle);
        }
    }

    /**
     * Returns all rows in the file.
     *
     * @return array
     */
    public function getRows()
    {
        return iterator_to_array($this);
    }

    /**
     * Maps each row using a callback function and returns the result.
     *
     * @param callable $callback
     *
     * @return array
     */
    public function map(callable $callback)
    {
        $result = [];
        foreach ($this as $row) {
            $result[] = $callback($row);
        }

        return $result;
    }

    /**
     * Returns an array of cells for the next row.
     *
     * @return array
     */
    protected function getNextRow()
    {
        $row = fgetcsv(
            $this->fileHandle,
            null,
            $this->args['delimiter'],
            $this->args['enclosure'],
            $this->args['escape']
        );

        return $row;
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        $row       = $this->row;
        $this->row = [];

        // Iterate through the row and replace each key with the relevant header
        foreach ($row as $index => $cell) {
            if ($this->args['headers'] && isset($this->headers[$index])) {
                $index = $this->headers[$index];
            }

            $this->row[$index] = $this->formatCell($cell);
        }

        return $this->row;
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        $this->row = $this->getNextRow();

        return $this->row !== false && $this->row !== null;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        rewind($this->fileHandle);
        $this->index = 0;

        if ($this->args['headers']) {
            $this->headers = $this->getNextRow();
        }
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->getRows());
    }

    /**
     * @param $cell
     *
     * @return mixed
     */
    protected function formatCell($cell)
    {
        if ($this->args['charset']) {
            $charset = mb_detect_encoding($cell);

            if (strcasecmp($charset, $this->args['charset']) !== 0) {
                $cell = mb_convert_encoding($cell, $this->args['charset']);
            }
        }

        $cell = str_replace(static::$newLines, PHP_EOL, $cell);

        if ($this->args['normalize']) {
            $cell = $this->normalizeValue($cell);
        }

        return $cell;
    }

    /**
     * Attempts to convert a string value to it's normalized data type.
     *
     * Numeric looking values get converted to ints or floats, truthy
     * and falsey looking values get converted to booleans.
     *
     * @param string $value
     *
     * @return mixed
     */
    protected function normalizeValue($value)
    {
        $trimmedValue = trim($value);

        // Number
        if (is_numeric($trimmedValue)) {
            // We add zero instead of typecasting to account
            // for both integers floats.
            return $trimmedValue + 0;
        }

        // Boolean
        $valueLowered = mb_strtolower($trimmedValue);
        if (in_array($valueLowered, $this->args['truthy'])) {
            return true;
        }

        if (in_array($valueLowered, $this->args['falsey'])) {
            return false;
        }

        return $value;
    }

    protected function parseArgs($args = [])
    {
        $parser = new ArgParser($args, 'file');
        $parser->parseSetters($this);

        return $parser->resolveOptions(self::$defaultArgs);
    }
}
