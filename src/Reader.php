<?php

declare(strict_types=1);

namespace Palmtree\Csv;

use Palmtree\Csv\Normalizer\NormalizerInterface;
use Palmtree\Csv\Normalizer\NullNormalizer;
use Palmtree\Csv\Row\Row;
use Palmtree\Csv\Util\StringUtil;

/**
 * Reads a CSV file by loading each line into memory one at a time.
 *
 * @implements \Iterator<int, Row>
 */
class Reader extends AbstractCsvDocument implements \Iterator
{
    /** @var class-string<NormalizerInterface> */
    private string $defaultNormalizer = NullNormalizer::class;
    private ?NormalizerInterface $headerNormalizer = null;
    /** @var array<NormalizerInterface> */
    private array $normalizers = [];
    private ?Row $headers = null;
    private ?Row $row = null;
    private ?string $stripBom = StringUtil::BOM_UTF8;
    private int $offset = 0;
    private int $headerOffset = 0;

    public static function read(string $filePath, bool $hasHeaders = true): self
    {
        return new self($filePath, $hasHeaders);
    }

    public function getHeaders(): ?Row
    {
        if ($this->hasHeaders && $this->headers === null) {
            $this->rewind();
        }

        return $this->headers;
    }

    public function getHeader(int|string $key): int|string
    {
        return $this->headers[$key] ?? $key;
    }

    public function setHeaderNormalizer(NormalizerInterface $headerNormalizer): self
    {
        $this->headerNormalizer = $headerNormalizer;

        return $this;
    }

    public function getHeaderNormalizer(): NormalizerInterface
    {
        return $this->headerNormalizer ??= new NullNormalizer();
    }

    public function addNormalizer(string $key, NormalizerInterface $normalizer): self
    {
        $this->normalizers[$key] = $normalizer;

        return $this;
    }

    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function addNormalizers(iterable $normalizers): self
    {
        foreach ($normalizers as $key => $normalizer) {
            $this->addNormalizer($key, $normalizer);
        }

        return $this;
    }

    public function getNormalizer(int|string $key): NormalizerInterface
    {
        if ($this->hasHeaders && \is_int($key)) {
            $this->normalizers[$key] = $this->getHeaderNormalizer();
        }

        if (!isset($this->normalizers[$key])) {
            $class = $this->getDefaultNormalizer();

            $this->normalizers[$key] = new $class();
        }

        return $this->normalizers[$key];
    }

    /**
     * @param class-string<NormalizerInterface> $defaultNormalizer
     */
    public function setDefaultNormalizer(string $defaultNormalizer): self
    {
        $this->defaultNormalizer = $defaultNormalizer;

        return $this;
    }

    /**
     * @return class-string<NormalizerInterface>
     */
    public function getDefaultNormalizer(): string
    {
        return $this->defaultNormalizer;
    }

    public function setStripBom(?string $stripBom): self
    {
        $this->stripBom = $stripBom;

        return $this;
    }

    public function setOffset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setHeaderOffset(int $headerOffset): self
    {
        $this->headerOffset = $headerOffset;

        return $this;
    }

    public function getHeaderOffset(): int
    {
        return $this->headerOffset;
    }

    public function current(): Row
    {
        \assert($this->row instanceof Row);

        return $this->row;
    }

    public function next(): void
    {
        $this->getDocument()->next();
    }

    public function key(): int
    {
        return $this->getDocument()->key();
    }

    public function valid(): bool
    {
        $this->row = $this->getCurrentRow();

        return $this->row instanceof Row;
    }

    public function rewind(): void
    {
        $this->getDocument()->rewind();

        $dataOffset = $this->offset + $this->headerOffset;
        if ($this->hasHeaders) {
            if ($this->headerOffset) {
                $this->getDocument()->seek($this->headerOffset);
            }

            // Set headers to null first so the header row is a zero-based array and can be used
            // to set the array keys of all other rows.
            $this->headers = null;
            $this->headers = $this->getCurrentRow();

            ++$dataOffset;
        }

        if ($dataOffset > 0) {
            $this->getDocument()->seek($dataOffset);
        }
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this as $row) {
            $result[] = $row->toArray();
        }

        return $result;
    }

    protected function getOpenMode(): string
    {
        return 'r';
    }

    /**
     * Reads the next line in the CSV file and returns a Row object from it.
     */
    private function getCurrentRow(): ?Row
    {
        $cells = $this->getDocument()->current();

        if (!\is_array($cells) || $cells == [null]) {
            return null;
        }

        if ($this->key() === 0 && $this->stripBom) {
            $stripped = StringUtil::stripBom($cells[0], $this->stripBom);

            if ($stripped !== $cells[0]) {
                $cells[0] = trim($stripped, $this->enclosure);
            }
        }

        return new Row($cells, $this);
    }
}
