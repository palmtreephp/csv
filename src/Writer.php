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
        $writer->closeFileHandle();
    }

    /**
     * @inheritDoc
     */
    public function closeFileHandle()
    {
        $this->trimTrailingLineEnding();

        parent::closeFileHandle();
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
        // We're setting headers so start again with a new file handle.
        $this->createFileHandle();

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
        if (!$this->getFileHandle()) {
            $this->createFileHandle();
        }

        $result = fwrite($this->getFileHandle(), $this->getCsvString($row));

        if ($result === false) {
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
        $result .= implode($glue, $this->escapeQuotes($row));

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
        if ($this->bytesWritten > 0 && $this->getFileHandle()) {
            // Only trim the file if it ends with the line ending delimiter.
            $length = mb_strlen($this->getLineEnding());

            fseek($this->getFileHandle(), -$length, SEEK_END);
            $chunk = fread($this->getFileHandle(), $length);

            if ($chunk === $this->getLineEnding()) {
                ftruncate($this->getFileHandle(), $this->bytesWritten - $length);
            }
        }
    }

    /**
     * Escapes double quotes recursively.
     * RFC-4180 states double quotes should be escaped with another double quote.
     *
     * @param mixed $data Array or string of data to escape.
     *
     * @return mixed Escaped data
     */
    protected function escapeQuotes($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->escapeQuotes($value);
            }
        } else {
            $data = str_replace($this->getEnclosure(), str_repeat($this->getEnclosure(), 2), $data);
        }

        return $data;
    }
}
