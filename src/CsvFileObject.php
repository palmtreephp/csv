<?php

namespace Palmtree\Csv;

class CsvFileObject extends \SplFileObject
{
    /** @var int */
    private $bytesWritten = 0;
    /** @var string */
    private $lineEnding = "\r\n";

    public function fwriteCsv(array $row)
    {
        $bytes = $this->fwrite($this->getCsvString($row));

        if ($bytes === false) {
            return false;
        }

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
     * @link https://tools.ietf.org/html/rfc4180#section-2
     */
    public function trimFinalLineEnding(): void
    {
        if ($this->bytesWritten > 0) {
            // Only trim the file if it ends with the line ending delimiter.
            $length = \strlen($this->lineEnding);

            $this->fseek(-$length, SEEK_END);

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
               \implode($enclosure . $delimiter . $enclosure, self::escapeEnclosure($row, $enclosure)) .
               $enclosure .
               $this->lineEnding;
    }

    /**
     * Escapes the enclosure character recursively.
     * RFC-4180 states the enclosure character (usually double quotes) should be
     * escaped by itself, so " becomes "".
     *
     * @link https://tools.ietf.org/html/rfc4180#section-2
     *
     * @param array|string $data Array or string of data to escape.
     *
     * @return array|string Escaped data.
     */
    private static function escapeEnclosure($data, string $enclosure)
    {
        if (\is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::escapeEnclosure($value, $enclosure);
            }
        } else {
            $data = \str_replace($enclosure, \str_repeat($enclosure, 2), $data);
        }

        return $data;
    }
}
