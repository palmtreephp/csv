<?php

declare(strict_types=1);

namespace Palmtree\Csv;

use Palmtree\Csv\Util\StringUtil;

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
    private bool $escapeFormulas = false;

    /**
     * @param iterable<array<array-key, mixed>> $data
     */
    public static function write(string $filePath, iterable $data): void
    {
        $writer = new self($filePath);
        $writer->setData($data);
        $writer->closeDocument();
    }

    /**
     * Sets headers and adds all rows to the CSV file.
     *
     * Uses the first row's keys as headers. Rows are written as they are iterated, so a generator
     * can write a large data set without holding it all in memory.
     *
     * @param iterable<array<array-key, mixed>> $data
     */
    public function setData(iterable $data): self
    {
        $isFirstRow = true;
        foreach ($data as $row) {
            if ($isFirstRow && $this->hasHeaders) {
                $this->setHeaders(array_keys($row));
            }

            $isFirstRow = false;
            $this->addRow($row);
        }

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
     * Sets whether headers and string values that start with =, +, -, @, tab or carriage return are
     * prefixed with a single quote, so spreadsheet applications show them as text instead of evaluating
     * them as formulas. Enable this when writing data from untrusted sources that will be opened in a
     * spreadsheet. Defaults to false, since the prefix becomes part of the value for other consumers.
     *
     * @see https://owasp.org/www-community/attacks/CSV_Injection
     */
    public function setEscapeFormulas(bool $escapeFormulas): self
    {
        $this->escapeFormulas = $escapeFormulas;

        return $this;
    }

    public function getEscapeFormulas(): bool
    {
        return $this->escapeFormulas;
    }

    /**
     * Adds multiple rows of data to the CSV file.
     *
     * @param iterable<array<array-key, mixed>> $rows
     */
    public function addRows(iterable $rows): void
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

        return $this->writeRow($this->orderByHeaders($row)) > 0;
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

    /**
     * Copies the CSV contents to a stream in chunks, so large files aren't loaded into memory.
     * Useful for building framework responses, e.g. writing to php://output in a Symfony
     * StreamedResponse or to a php://temp stream for a PSR-7 response body.
     *
     * @param resource $stream A stream opened for writing.
     *
     * @return int The number of bytes written.
     */
    public function writeTo($stream): int
    {
        if (!\is_resource($stream) || get_resource_type($stream) !== 'stream') {
            throw new \InvalidArgumentException(\sprintf('Expected a stream resource, got %s.', get_debug_type($stream)));
        }

        $this->writeHeaders();

        $document = $this->getDocument();
        $document->fseek(0);

        $bytes = 0;
        try {
            while (!$document->eof()) {
                $chunk = $document->fread(8192);

                if ($chunk === false || $chunk === '') {
                    break;
                }

                if (fwrite($stream, $chunk) !== \strlen($chunk)) {
                    throw new \RuntimeException('Failed to write CSV contents to stream.');
                }

                $bytes += \strlen($chunk);
            }
        } finally {
            $document->fseek(0, \SEEK_END);
        }

        return $bytes;
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

        $this->writeRow($this->headers);
    }

    /**
     * @param array<array-key, mixed> $row
     */
    private function writeRow(array $row): int
    {
        if ($this->escapeFormulas) {
            $row = StringUtil::escapeFormulas($row);
        }

        return $this->getDocument()->fwriteCsv($row);
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
