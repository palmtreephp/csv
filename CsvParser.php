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
     * @var array
     */
    public static $defaultArgs = [
        'charset'      => 'utf-8',
        'file'         => '',
        'hasHeaders'   => true,
        'delimiter'    => ',',
        'enclosure'    => '"',
        'escape'       => '\\',
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
     * @var
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
        $this->fileHandle = fopen($this->args['file'], 'r');
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
     * @return array
     */
    protected function getNextRow()
    {
        return fgetcsv($this->fileHandle, null, $this->args['delimiter']);
    }

    /**
     * @return array
     */
    public function current()
    {
        $row       = $this->row;
        $this->row = [];

        foreach ($row as $key => $cell) {
            if ($this->args['hasHeaders'] && isset($this->headers[$key])) {
                $key = $this->headers[$key];
            }

            $this->row[$key] = $this->formatCell($cell);
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
     * @param $value
     *
     * @return mixed
     */
    protected function normalizeValue($value)
    {
        $trimmedValue = trim($value);

        // Number
        if (is_numeric($trimmedValue)) {
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

    /**
     * @return mixed
     */
    public function count()
    {
        return count($this->getRows());
    }

    protected function parseArgs($args = [])
    {
        $parser = new ArgParser($args, 'filename');
        $parser->parseSetters($this);

        return $parser->resolveOptions(self::$defaultArgs);
    }

}
