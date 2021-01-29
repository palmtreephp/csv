<?php

namespace Palmtree\Csv;

/**
 * Writes an array to a CSV file.
 */
class Writer extends AbstractCsvDocument
{
    /** @var array */
    private $headers = [];

    public static function write(string $filePath, array $data): void
    {
        $writer = new self($filePath);
        $writer->setData($data);
        $writer->closeDocument();
    }

    /**
     * Sets headers and all rows on the CSV file and
     * then closes the file handle.
     *
     * Uses the first row's keys as headers.
     */
    public function setData(array $data): self
    {
        if ($this->hasHeaders) {
            $this->setHeaders(array_keys(reset($data)));
        }

        $this->addRows($data);

        return $this;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        $this->addRow($this->headers);

        return $this;
    }

    public function addHeader(string $header): self
    {
        $headers = $this->headers;

        $headers[] = $header;

        $this->setHeaders($headers);

        return $this;
    }

    /**
     * Adds multiple rows of data to the CSV file.
     */
    public function addRows(array $rows): void
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
    public function addRow(array $row): bool
    {
        if ($this->hasHeaders && empty($this->headers)) {
            $this->setHeaders(array_keys($row));
        }

        $result = $this->getDocument()->fwriteCsv($row);

        if ($result === 0) {
            // @todo: handle error
            return false;
        }

        return true;
    }

    public function getContents(): string
    {
        $this->getDocument()->trimFinalLineEnding();

        $size = $this->getDocument()->getSize();

        if ($size === 0) {
            return '';
        }

        $this->getDocument()->fseek(0);

        $data = $this->getDocument()->fread($size);

        if ($data === false) {
            return '';
        }

        return $data;
    }

    protected function getOpenMode(): string
    {
        return 'w+';
    }
}
