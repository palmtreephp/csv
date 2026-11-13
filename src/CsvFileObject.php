<?php

declare(strict_types=1);

namespace Palmtree\Csv;

use Palmtree\Csv\Util\StringUtil;

class CsvFileObject extends \SplFileObject
{
    private int $bytesWritten = 0;
    private string $lineEnding = "\r\n";

    /**
     * Writes a row to the file. The line ending is written before every row except the first,
     * so the file never ends with a trailing new line, as RFC-4180 recommends.
     *
     * @see https://tools.ietf.org/html/rfc4180#section-2
     */
    public function fwriteCsv(array $row): int
    {
        $line = $this->getCsvString($row);

        if ($this->bytesWritten > 0) {
            $line = $this->lineEnding . $line;
        }

        $bytes = $this->fwrite($line);

        if ($bytes === false) {
            throw new \RuntimeException('Failed to write CSV row to file.');
        }

        $this->bytesWritten += $bytes;

        return $bytes;
    }

    public function getBytesWritten(): int
    {
        return $this->bytesWritten;
    }

    public function getLineEnding(): string
    {
        return $this->lineEnding;
    }

    public function setLineEnding(string $lineEnding): self
    {
        $this->lineEnding = $lineEnding;

        return $this;
    }

    public function getSize(): int
    {
        try {
            $size = parent::getSize();
        } catch (\RuntimeException) {
            $size = $this->fstat()['size'];
        }

        return $size;
    }

    /**
     * Returns a string representation of a row to be written as a line in a CSV file.
     */
    private function getCsvString(array $row): string
    {
        [$delimiter, $enclosure] = $this->getCsvControl();

        return $enclosure .
               implode($enclosure . $delimiter . $enclosure, StringUtil::escapeEnclosure($row, $enclosure)) .
               $enclosure;
    }
}
