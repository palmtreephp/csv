<?php

namespace Palmtree\Csv;

class CsvFileObject extends \SplFileObject
{
    /** @var int */
    private $bytesWritten = 0;
    /** @var string */
    private $lineEnding = "\r\n";

    public function fwriteCsv(array $row, ?string $delimiter = null, ?string $enclosure = null)
    {
        $bytes = $this->fwrite($this->getCsvString($row, $delimiter, $enclosure));

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

    /**
     * Returns a string representation of a row to be written as a line in a CSV file.
     */
    protected function getCsvString(array $row, ?string $delimiter = null, ?string $enclosure = null): string
    {
        $csvControl = $this->getCsvControl();

        $delimiter = $delimiter ?: $csvControl[0];
        $enclosure = $enclosure ?: $csvControl[1];

        $result = $enclosure;
        $result .= \implode($enclosure . $delimiter . $enclosure, self::escapeEnclosure($row, $enclosure));
        $result .= $enclosure;

        $result .= $this->lineEnding;

        return $result;
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
     * Escapes the enclosure character recursively.
     * RFC-4180 states the enclosure character (usually double quotes) should be
     * escaped by itself, so " becomes "".
     *
     * @param mixed $data Array or string of data to escape.
     *
     * @return mixed Escaped data
     */
    protected static function escapeEnclosure($data, string $enclosure)
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
