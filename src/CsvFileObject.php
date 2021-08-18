<?php

declare(strict_types=1);

namespace Palmtree\Csv;

use Palmtree\Csv\Util\StringUtil;

class CsvFileObject extends \SplFileObject
{
    private int $bytesWritten = 0;
    private string $lineEnding = "\r\n";

    public function fwriteCsv(array $row): int
    {
        $bytes = $this->fwrite($this->getCsvString($row));

        $this->bytesWritten += $bytes;

        return $bytes;
    }

    public function __destruct()
    {
        $this->trimFinalLineEnding();
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
        } catch (\RuntimeException $exception) {
            $size = $this->fstat()['size'];
        }

        return $size;
    }

    /**
     * Trims the line ending delimiter from the end of the CSV file.
     * RFC-4180 states CSV files should not contain a trailing new line.
     *
     * @see https://tools.ietf.org/html/rfc4180#section-2
     */
    public function trimFinalLineEnding(): void
    {
        if ($this->bytesWritten > 0) {
            // Only trim the file if it ends with the line ending delimiter.
            $length = \strlen($this->lineEnding);

            $this->fseek(-$length, \SEEK_END);

            if ($this->fread($length) === $this->lineEnding) {
                $this->ftruncate($this->bytesWritten - $length);
            }
        }
    }

    /**
     * Returns a string representation of a row to be written as a line in a CSV file.
     */
    private function getCsvString(array $row): string
    {
        list($delimiter, $enclosure) = $this->getCsvControl();

        return $enclosure .
               implode($enclosure . $delimiter . $enclosure, StringUtil::escapeEnclosure($row, $enclosure)) .
               $enclosure .
               $this->lineEnding;
    }
}
