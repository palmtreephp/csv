<?php

namespace Palmtree\Csv;

/**
 * Writes an array to a CSV file.
 */
class Writer extends AbstractCsv
{
    /** @var string */
    protected $fopenMode = 'w+';
    /** @var string */
    protected $lineEnding = "\r\n";
    /** @var int */
    protected $bytesWritten = 0;

    public static function write($file, $data)
    {
        $writer = new static($file);
        $writer->setData($data);
        $writer->closeDocument();
    }

    /**
     * @inheritDoc
     */
    public function closeDocument()
    {
        $this->trimTrailingLineEnding();

        parent::closeDocument();
    }

    /**
     * Sets headers and all rows on the CSV file and
     * then closes the file handle.
     *
     * @param $data
     */
    public function setData($data)
    {
        if ($this->hasHeaders()) {
            $this->setHeaders(array_keys(reset($data)));
        }

        $this->addRows($data);
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->createDocument();

        $this->addRow($headers);
    }

    /**
     * Adds multiple rows of data to the CSV file.
     *
     * @param array $rows
     */
    public function addRows($rows)
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }
    }

    /**
     * Adds a row of data to the CSV file.
     *
     * @param array $row Row of data to add to the file.
     *
     * @return bool Whether the row was written to the file.
     */
    public function addRow($row)
    {
        if (!$this->getDocument()) {
            $this->createDocument();
        }

        $result = $this->getDocument()->fwrite($this->getCsvString($row));

        if ($result === null) {
            // @todo: handle error
            return false;
        }

        $this->bytesWritten += $result;

        return true;
    }

    /**
     * Returns the line ending delimiter used to separate rows
     * in the CSV file.
     *
     * @return string
     */
    public function getLineEnding()
    {
        return $this->lineEnding;
    }

    /**
     * Sets the line ending delimiter used to separate rows
     * in the CSV file.
     *
     * @param string $lineEnding
     *
     * @return Writer
     */
    public function setLineEnding($lineEnding)
    {
        $this->lineEnding = $lineEnding;

        return $this;
    }

    /**
     * Returns a string representation of a row to be written
     * as a line in a CSV file.
     *
     * @param array $row
     *
     * @return string
     */
    protected function getCsvString($row)
    {
        $result = $this->getEnclosure();

        $glue   = $this->getEnclosure() . $this->getDelimiter() . $this->getEnclosure();
        $result .= implode($glue, $this->escapeEnclosure($row));

        $result .= $this->getEnclosure();
        $result .= $this->getLineEnding();

        return $result;
    }

    /**
     * Trims the line ending delimiter from the end of the CSV file.
     * RFC-4180 states CSV files should not contain a trailing new line.
     */
    protected function trimTrailingLineEnding()
    {
        if ($this->bytesWritten > 0 && $this->getDocument()) {
            // Only trim the file if it ends with the line ending delimiter.
            $length = mb_strlen($this->getLineEnding());

            $this->getDocument()->fseek(-$length, SEEK_END);
            $chunk = $this->getDocument()->fread($length);

            if ($chunk === $this->getLineEnding()) {
                $this->getDocument()->ftruncate($this->bytesWritten - $length);
            }
        }
    }

    /**
     * Escapes the enclosure character recursively.
     * RFC-4180 states the enclosure character (usually double quotes) should be
     * escaped by itself, so " becomes "".
     *
     * @param mixed $data Array or string of data to escape.
     *
     * @return mixed Escaped data
     */
    protected function escapeEnclosure($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->escapeEnclosure($value);
            }
        } else {
            $data = str_replace($this->getEnclosure(), str_repeat($this->getEnclosure(), 2), $data);
        }

        return $data;
    }
}
