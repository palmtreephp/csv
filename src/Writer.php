<?php

namespace Palmtree\Csv;

/**
 * Writes an array to a CSV file.
 */
class Writer extends AbstractCsv
{
    /** @var string */
    protected $openMode = 'w+';

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
     * @param $data
     *
     * @return $this
     */
    public function setData($data)
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

        $this->addRow($headers);

        return $this;
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
        $result = $this->getDocument()->fputcsv($row);

        if ($result === null) {
            // @todo: handle error
            return false;
        }

        return true;
    }
}
