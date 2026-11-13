<?php

declare(strict_types=1);

namespace Palmtree\Csv;

/**
 * Writes an array to a CSV file.
 *
 * Headers are written to the file just before the first row, or when the contents are read
 * or the document is closed, so they can be changed until then.
 */
class Writer extends AbstractCsvDocument
{
    /** @var list<array-key> */
    private array $headers = [];
    private bool $headersWritten = false;

    /**
     * @param array<array<array-key, mixed>> $data
     */
    public static function write(string $filePath, array $data): void
    {
        $writer = new self($filePath);
        $writer->setData($data);
        $writer->closeDocument();
    }

    /**
     * Sets headers and adds all rows to the CSV file.
     *
     * Uses the first row's keys as headers.
     *
     * @param array<array<array-key, mixed>> $data
     */
    public function setData(array $data): self
    {
        if ($data === []) {
            return $this;
        }

        $firstRow = reset($data);

        if ($this->hasHeaders) {
            $this->setHeaders(array_keys($firstRow));
        }

        $this->addRows($data);

        return $this;
    }

    /**
     * @param array<array-key> $headers
     */
    public function setHeaders(array $headers): self
    {
        if ($this->headersWritten) {
            throw new \LogicException('Headers cannot be changed after they have been written to the document.');
        }

        $this->headers = array_values($headers);

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
     *
     * @param array<array<array-key, mixed>> $rows
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
     * If the row has string keys and headers are set, its values are written in header order.
     * Missing keys are written as empty cells.
     *
     * @param array<array-key, mixed> $row Row of data to add to the file.
     *
     * @return bool Whether the row was written to the file.
     */
    public function addRow(array $row): bool
    {
        if ($this->hasHeaders && $this->headers === [] && !$this->headersWritten) {
            $this->setHeaders(array_keys($row));
        }

        $this->writeHeaders();

        return $this->getDocument()->fwriteCsv($this->orderByHeaders($row)) > 0;
    }

    public function getContents(): string
    {
        $this->writeHeaders();

        if (($size = $this->getDocument()->getSize()) === 0) {
            return '';
        }

        $this->getDocument()->fseek(0);

        $data = $this->getDocument()->fread($size);

        $this->getDocument()->fseek(0, \SEEK_END);

        return $data === false ? '' : $data;
    }

    #[\Override]
    public function closeDocument(): void
    {
        if ($this->hasDocument()) {
            $this->writeHeaders();
        }

        parent::closeDocument();
    }

    #[\Override]
    public function __destruct()
    {
        $this->writeHeaders();

        parent::__destruct();
    }

    protected function getOpenMode(): string
    {
        return 'w+';
    }

    protected function writeHeaders(): void
    {
        if ($this->headersWritten || $this->headers === []) {
            return;
        }

        $this->headersWritten = true;

        $this->getDocument()->fwriteCsv($this->headers);
    }

    /**
     * @param array<array-key, mixed> $row
     *
     * @return array<array-key, mixed>
     */
    private function orderByHeaders(array $row): array
    {
        if ($this->headers === [] || array_is_list($row)) {
            return $row;
        }

        $unknownKeys = array_diff(array_keys($row), $this->headers);

        if ($unknownKeys !== []) {
            throw new \InvalidArgumentException(\sprintf('Row contains keys that are not in the headers: %s', implode(', ', $unknownKeys)));
        }

        $ordered = [];
        foreach ($this->headers as $header) {
            $ordered[] = $row[$header] ?? '';
        }

        return $ordered;
    }
}
