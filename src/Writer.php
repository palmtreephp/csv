<?php

namespace Palmtree\Csv;

/**
 * Writes an array to a CSV file.
 */
class Writer extends AbstractCsv
{
    /** @var string */
    protected $openMode = 'w+';
    /** @var array */
    protected $headers = [];

    public static function write($file, $data)
    {
        $writer = new static($file);
        $writer->setData($data);
        $writer->closeDocument();
    }

    /**
     * Sets headers and all rows on the CSV file and
     * then closes the file handle.
     *
     * Uses the first row's keys as headers.
     *
     * @param $data
     *
     * @return $this
     */
    public function setData(array $data)
    {
        if ($this->hasHeaders()) {
            $this->setHeaders(array_keys(reset($data)));
        }

        $this->addRows($data);

        return $this;
    }

    /**
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->createDocument();

        $this->headers = $headers;

        $this->addRow($this->headers);

        return $this;
    }

    /**
     * @param string $header
     *
     * @return Writer
     */
    public function addHeader($header)
    {
        $headers = $this->headers;

        $headers[] = $header;

        $this->setHeaders($headers);

        return $this;
    }

    /**
     * Adds multiple rows of data to the CSV file.
     *
     * @param array $rows
     */
    public function addRows(array $rows)
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
    public function addRow(array $row)
    {
        $result = $this->getDocument()->fwriteCsv($row);

        if ($result === false) {
            // @todo: handle error
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        $this->getDocument()->trimFinalLineEnding();
        $this->getDocument()->fseek(0);

        return $this->getDocument()->fread($this->getDocument()->getSize());
    }
}
