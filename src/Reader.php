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
    public static $defaultFormatter = StringFormatter::class;

    protected $openMode = 'r';
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
     * @return Reader
     */
    public static function read($file)
    {
        $csv = new static($file);

        return $csv;
    }

    public function createDocument()
    {
        parent::createDocument();

        $this->getDocument()->setCsvControl($this->getDelimiter(), $this->getEnclosure(), $this->getEscapeCharacter());
    }

    /**
     * @return Row
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeader($key)
    {
        if (!isset($this->headers[$key])) {
            return $key;
        }

        return $this->headers[$key];
    }

    /**
     * @param mixed              $key
     * @param FormatterInterface $formatter Formatter instance.
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
     * @return FormatterInterface
     */
    public function getFormatter($key)
    {
        if (!isset($this->formatters[$key])) {
            $class = static::$defaultFormatter;

            $this->formatters[$key] = new $class();
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
     * Reads the next line in the CSV file
     * and returns a Row object from it.
     *
     * @return Row
     */
    protected function getNextRow()
    {
        $cells = $this->getDocument()->current();

        if (!is_array($cells)) {
            return null;
        }

        $row = new Row($cells, $this);

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
        $this->getDocument()->next();

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
        $this->getDocument()->rewind();

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
