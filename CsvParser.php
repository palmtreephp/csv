<?php

namespace Palmtree\Csv;

use Palmtree\ArgParser\ArgParser;

/**
 * Class CsvParser
 * @package    Palmtree
 * @subpackage Csv
 */
class CsvParser implements \Iterator
{
    /**
     * @var array
     */
    public static $defaultArgs = [
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
    protected $line;

    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var array
     */
    protected $newLines = ["\r\n", "\r", "\n"];

    /**
     * CSV constructor.
     */
    public function __construct($args = [])
    {
        if (is_string($args)) {
            $args = ['file' => $args];
        }

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
     * Returns all lines in the file.
     *
     * @return array
     */
    public function getLines()
    {
        $result = [];

        foreach ($this as $line) {
            $result[] = $line;
        }

        return $result;
    }

    /**
     * Maps each line using a callback function and returns the result.
     *
     * @param callable $callback
     *
     * @return array
     */
    public function map(callable $callback)
    {
        $result = [];
        foreach ($this as $line) {
            $result[] = $callback($line);
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getNextLine()
    {
        return fgetcsv($this->fileHandle, null, $this->args['delimiter']);
    }

    /**
     * @return array
     */
    public function current()
    {
        $line       = $this->line;
        $this->line = [];

        foreach ($line as $key => $cell) {
            if ($this->args['hasHeaders'] && isset($this->headers[$key])) {
                $key = $this->headers[$key];
            }

            $this->line[$key] = $this->formatCell($cell);
        }

        return $this->line;
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
        $this->line = $this->getNextLine();

        return $this->line !== false && $this->line !== null;
    }

    /**
     *
     */
    public function rewind()
    {
        rewind($this->fileHandle);

        if ($this->args['hasHeaders']) {
            $this->headers = $this->getNextLine();
        }
    }

    /**
     * @param      $cell
     * @param bool $toLower
     *
     * @return mixed
     */
    protected function formatCell($cell, $toLower = false)
    {
        $cell = trim($cell);
        $cell = str_replace($this->newLines, "\n", mb_convert_encoding($cell, 'UTF-8', mb_detect_encoding($cell)));

        if ($toLower) {
            $cell = mb_strtolower($cell);
        }

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
        // Number
        if (is_numeric($value)) {
            return $value + 0;
        }

        // Boolean
        $valueLowered = mb_strtolower($value);
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
        $parser = new ArgParser($args, 'filename');
        $parser->parseSetters($this);

        return $parser->resolveOptions(self::$defaultArgs);
    }
}
