<?php

namespace Palmtree\Csv;

use Palmtree\Csv\Formatter\FormatterInterface;
use Palmtree\Csv\Formatter\NullFormatter;
use Palmtree\Csv\Row\Row;

/**
 * Reads a CSV file by loading each line into memory
 * one at a time.
 */
class Reader extends AbstractCsv implements \Iterator
{
    /** @var string */
    protected $defaultFormatter = NullFormatter::class;
    /** @var string */
    protected $openMode = 'r';
    /** @var FormatterInterface[] */
    protected $formatters = [];
    /** @var Row */
    protected $headers;
    /** @var Row */
    protected $row;

    /**
     * @param string $file
     *
     * @return Reader
     */
    public static function read($file)
    {
        $csv = new static($file);

        return $csv;
    }

    /**
     * @return Row
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
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
            $class = $this->getDefaultFormatter();

            $this->formatters[$key] = new $class();
        }

        return $this->formatters[$key];
    }

    /**
     * Reads the next line in the CSV file
     * and returns a Row object from it.
     *
     * @return Row|null
     */
    protected function getCurrentRow()
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
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->getDocument()->key();
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        $this->row = $this->getCurrentRow();

        return $this->row instanceof Row;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->getDocument()->rewind();

        if ($this->hasHeaders()) {
            $this->headers = $this->getCurrentRow();
        }
    }

    /**
     * @param string $defaultFormatter
     *
     * @return Reader
     */
    public function setDefaultFormatter($defaultFormatter)
    {
        $this->defaultFormatter = $defaultFormatter;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultFormatter()
    {
        return $this->defaultFormatter;
    }
}
