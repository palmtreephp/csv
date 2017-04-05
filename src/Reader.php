<?php

namespace Palmtree\Csv;

use Palmtree\ArgParser\ArgParser;
use Palmtree\Csv\Formatter\FormatterInterface;
use Palmtree\Csv\Formatter\StringFormatter;

/**
 * Reads a CSV file by loading each line into memory
 * one at a time.
 */
class Reader implements \Iterator, \Countable
{
    /**
     * @var array   $defaultArgs {
     * @type string $file        Path to CSV file to parse.
     * @type bool   $headers     Whether the CSV file contains headers. Default true
     * @type string $delimiter   Cell delimiter. Default ',' (comma)
     * @type string $enclosure   Cell enclosure. Default '"' (double quote)
     * @type string $escape      Escape character. Default '\' (backslash)
     * }
     */
    public static $defaultArgs = [
        'file'      => '',
        'headers'   => true,
        'delimiter' => ',',
        'enclosure' => '"',
        'escape'    => '\\',
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

    /** @var FormatterInterface[] */
    protected $formatters = [];

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
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    public function addFormatter($key, FormatterInterface $formatter)
    {
        $this->formatters[$key] = $formatter;

        return $this;
    }

    public function addFormatters($formatters)
    {
        foreach ($formatters as $key => $formatter) {
            $this->addFormatter($key, $formatter);
        }

        return $this;
    }

    /**
     * @param mixed $key
     *
     * @return null|FormatterInterface
     */
    public function getFormatter($key)
    {
        if (! isset($this->formatters[$key])) {
            $this->formatters[$key] = new StringFormatter();
        }

        return $this->formatters[$key];
    }

    /**
     * Returns an array of cells for the next row.
     *
     * @param bool $raw
     *
     * @return Row
     */
    protected function getNextRow($raw = false)
    {
        $row = fgetcsv(
            $this->fileHandle,
            null,
            $this->args['delimiter'],
            $this->args['enclosure'],
            $this->args['escape']
        );

        if ($raw || ! $row) {
            return $row;
        }

        $row = new Row($row, $this);

        return $row;
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return $this->row;
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        ++$this->index;
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
            $this->headers = $this->getNextRow(true);
        }
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count(iterator_to_array($this));
    }

    protected function parseArgs($args = [])
    {
        $parser = new ArgParser($args, 'file');
        $parser->parseSetters($this);

        return $parser->resolveOptions(self::$defaultArgs);
    }
}
