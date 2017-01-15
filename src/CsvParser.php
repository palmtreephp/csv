<?php

namespace Palmtree\Csv;

use Palmtree\ArgParser\ArgParser;

/**
 * Class CsvParser
 * @package    Palmtree
 * @subpackage Csv
 */
class CsvParser implements \Iterator, \Countable
{
    /**
     * @var array   $defaultArgs  {
     * @type string $charset
     * @type bool   $hasHeaders   Whether the CSV file contains headers. Default true
     * @type string $delimiter    Cell delimiter, default ',' (comma)
     * @type string $enclosure    Cell enclosure, default '"' (double quote)
     * @type string $escape       Escape character. Default '\' (backslash)
     * @type string $file         File to parse.
     * @type bool   $normalize    Whether to normalize cell value data types. Default false
     * @type array  $falseyValues Array of falsey values to convert to boolean false.
     *                            Default ['false', 'off', 'no', '0', 'disabled']
     * @type array  $truthyValues Array of truthy values to convert to boolean true.
     *                            Default ['true', 'on', 'yes', '1', 'enabled']
     * }
     */
    public static $defaultArgs = [
        'charset'      => 'utf-8',
        'hasHeaders'   => true,
        'delimiter'    => ',',
        'enclosure'    => '"',
        'escape'       => '\\',
        'file'         => '',
        'normalize'    => false,
        'falseyValues' => ['false', 'off', 'no', '0', 'disabled'],
        'truthyValues' => ['true', 'on', 'yes', '1', 'enabled'],
    ];

    /**
     * @var resource
     */
    protected $fileHandle;
    /**
     * @var array
     */
    protected $headers = [];
    /**
     * @var int
     */
    protected $index = 0;
    /**
     * @var array
     */
    protected $row;

    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var array
     */
    protected static $newLines = ["\r\n", "\r", "\n"];

    /**
     * CSV constructor.
     */
    public function __construct($args = [])
    {
        $this->args = $this->parseArgs($args);

        ini_set('auto_detect_line_endings', '1');
        $this->fileHandle = @fopen($this->args['file'], 'r');

        if (! $this->fileHandle) {
            throw new \InvalidArgumentException(sprintf('File %s does not exist', $this->args['file']));
        }
    }

    /**
     *
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
     * Returns an array of cells for next row.
     *
     * @return array
     */
    protected function getNextRow()
    {
        return fgetcsv(
            $this->fileHandle,
            null,
            $this->args['delimiter'],
            $this->args['enclosure'],
            $this->args['escape']
        );
    }

    /**
     * @return array
     */
    public function current()
    {
        $row       = $this->row;
        $this->row = [];

        // Iterate through the row and replace each key with the relevant header
        foreach ($row as $index => $cell) {
            if ($this->args['hasHeaders'] && isset($this->headers[$index])) {
                $index = $this->headers[$index];
            }

            $this->row[$index] = $this->formatCell($cell);
        }

        return $this->row;
    }

    /**
     *
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        $this->row = $this->getNextRow();

        return $this->row !== false && $this->row !== null;
    }

    /**
     *
     */
    public function rewind()
    {
        rewind($this->fileHandle);

        if ($this->args['hasHeaders']) {
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

            if ($charset !== $this->args['charset']) {
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
            /*
             * We don't typecast to an integer here in case the
             * value is a float.
             */
            return $trimmedValue + 0;
        }

        // Boolean
        $valueLowered = mb_strtolower($trimmedValue);
        if (in_array($valueLowered, $this->args['truthyValues'])) {
            return true;
        }

        if (in_array($valueLowered, $this->args['falseyValues'])) {
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
