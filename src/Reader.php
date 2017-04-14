<?php

namespace Palmtree\Csv;

use Palmtree\Csv\Formatter\FormatterInterface;
use Palmtree\Csv\Formatter\StringFormatter;
use Palmtree\Csv\Row\Row;

/**
 * Reads a CSV file by loading each line into memory
 * one at a time.
 */
class Reader extends AbstractCsv implements \Iterator, \Countable
{
    protected $fopenMode = 'r';
    /** @var FormatterInterface[] */
    protected $formatters = [];
    /** @var int */
    protected $index = 0;
    /** @var Row */
    protected $headers;
    /** @var Row */
    protected $row;
    /** @var string */
    protected $escapeCharacter = "\0";

    /**
     * @param $file
     *
     * @return array
     */
    public static function read($file)
    {
        $csv = new static($file);

        return iterator_to_array($csv);
    }

    /**
     * @return Row
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param mixed              $key
     * @param FormatterInterface $formatter
     *
     * @return $this
     */
    public function addFormatter($key, FormatterInterface $formatter)
    {
        $this->formatters[$key] = $formatter;

        return $this;
    }

    /**
     * @param array|\Traversable $formatters
     *
     * @return $this
     */
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
        if (!isset($this->formatters[$key])) {
            $this->formatters[$key] = new StringFormatter();
        }

        return $this->formatters[$key];
    }

    /**
     * @return string
     */
    public function getEscapeCharacter()
    {
        return $this->escapeCharacter;
    }

    /**
     * @param string $escapeCharacter
     *
     * @return AbstractCsv
     */
    public function setEscapeCharacter($escapeCharacter)
    {
        $this->escapeCharacter = $escapeCharacter;

        return $this;
    }

    /**
     * Returns an array of cells for the next row.
     *
     * @return Row
     */
    protected function getNextRow()
    {
        if (!$this->getFileHandle()) {
            $this->createFileHandle();
        }

        $row = fgetcsv(
            $this->getFileHandle(),
            null,
            $this->getDelimiter(),
            $this->getEnclosure(),
            $this->getEscapeCharacter()
        );

        if ($row === null || $row === false) {
            return null;
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

        return $this->row instanceof Row;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        if ($this->getFileHandle()) {
            rewind($this->getFileHandle());
        }

        $this->index = 0;

        if ($this->hasHeaders()) {
            $this->headers = $this->getNextRow();
        }
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count(iterator_to_array($this));
    }
}
