<?php

namespace Palmtree\Csv;

use Palmtree\Csv\Normalizer\NormalizerInterface;
use Palmtree\Csv\Normalizer\NullNormalizer;
use Palmtree\Csv\Row\Row;
use Palmtree\Csv\Util\StringUtil;

/**
 * Reads a CSV file by loading each line into memory one at a time.
 */
class Reader extends AbstractCsv implements \Iterator
{
    /** @var string */
    private $defaultNormalizer = NullNormalizer::class;
    /** @var NormalizerInterface */
    private $headerNormalizer;
    /** @var NormalizerInterface[] */
    private $normalizers = [];
    /** @var Row */
    private $headers;
    /** @var Row */
    private $row;
    /** @var string|null */
    private $stripBom = StringUtil::BOM_UTF8;
    /** @var int */
    private $offset = 0;
    /** @var int */
    private $headerOffset = 0;

    public function __construct(string $file)
    {
        $this->headerNormalizer = new NullNormalizer();
        parent::__construct($file);
    }

    public function getOpenMode(): string
    {
        return 'r';
    }

    public static function read(string $file): self
    {
        return new self($file);
    }

    public function getHeaders(): Row
    {
        if (null === $this->headers && $this->hasHeaders) {
            $this->rewind();
        }

        return $this->headers;
    }

    public function getHeader(string $key): string
    {
        if (!isset($this->headers[$key])) {
            return $key;
        }

        return $this->headers[$key];
    }

    public function setHeaderNormalizer(NormalizerInterface $headerNormalizer): self
    {
        $this->headerNormalizer = $headerNormalizer;

        return $this;
    }

    public function addNormalizer(string $key, NormalizerInterface $normalizer): self
    {
        $this->normalizers[$key] = $normalizer;

        return $this;
    }

    public function addNormalizers(iterable $normalizers): self
    {
        foreach ($normalizers as $key => $normalizer) {
            $this->addNormalizer($key, $normalizer);
        }

        return $this;
    }

    /**
     * @param mixed $key
     */
    public function getNormalizer($key): NormalizerInterface
    {
        if ($this->hasHeaders && \is_int($key)) {
            $this->normalizers[$key] = $this->headerNormalizer;
        }

        if (!isset($this->normalizers[$key])) {
            $class = $this->getDefaultNormalizer();

            $this->normalizers[$key] = new $class();
        }

        return $this->normalizers[$key];
    }

    /**
     * Reads the next line in the CSV file and returns a Row object from it.
     */
    protected function getCurrentRow(): ?Row
    {
        $cells = $this->getDocument()->current();

        if (!\is_array($cells) || $cells == [null]) {
            return null;
        }

        if ($this->key() === 0 && $this->stripBom) {
            $stripped = StringUtil::stripBom($cells[0], $this->stripBom);

            if ($stripped !== $cells[0]) {
                $cells[0] = \trim($stripped, $this->enclosure);
            }
        }

        return new Row($cells, $this);
    }

    /**
     * @inheritDoc
     */
    public function current(): Row
    {
        return $this->row;
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        $this->getDocument()->next();
    }

    /**
     * @inheritDoc
     */
    public function key(): int
    {
        return $this->getDocument()->key();
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        $this->row = $this->getCurrentRow();

        return $this->row instanceof Row;
    }

    /**
     * @inheritDoc
     */
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

    public function setDefaultNormalizer(string $defaultNormalizer): self
    {
        $this->defaultNormalizer = $defaultNormalizer;

        return $this;
    }

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

    public function toArray(): array
    {
        $result = [];
        foreach ($this as $rowKey => $row) {
            $result[$rowKey] = $row->toArray();
        }

        return $result;
    }
}
