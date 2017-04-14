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
     * @param $row
     *
     * @return bool
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
     * Truncates the last line ending from the file
     * before closing the handle.
     */
    public function closeFileHandle()
    {
        if ($this->bytesWritten > 0 && $this->getFileHandle()) {
            ftruncate($this->getFileHandle(), $this->bytesWritten - 1);
        }

        parent::closeFileHandle();
    }

    /**
     * Returns the new line delimiter used to separate rows
     * in the CSV file.
     *
     * @return string
     */
    public function getLineEnding()
    {
        return $this->lineEnding;
    }

    /**
     * Sets the new line delimiter used to separate rows
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
     * @param array $row
     *
     * @return string
     */
    protected function getCsvString($row)
    {
        $result = $this->getEnclosure();
        $result .= implode($this->getEnclosure() . $this->getDelimiter() . $this->getEnclosure(), $this->escape($row));
        $result .= $this->getEnclosure();
        $result .= $this->getLineEnding();

        return $result;
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    protected function escape($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->escape($value);
            }
        } else {
            $data = str_replace($this->getEnclosure(), str_repeat($this->getEnclosure(), 2), $data);
        }

        return $data;
    }
}
