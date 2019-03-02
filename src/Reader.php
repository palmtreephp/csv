<?php

namespace Palmtree\Csv;

use Palmtree\Csv\Normalizer\NormalizerInterface;
use Palmtree\Csv\Normalizer\NullNormalizer;
use Palmtree\Csv\Row\Row;
use Palmtree\Csv\Util\StringUtil;

/**
 * Reads a CSV file by loading each line into memory
 * one at a time.
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
    /** @var bool */
    private $bom = false;
    /** @var int */
    private $offset = 0;
    /** @var int */
    private $headerOffset = 0;

    public function __construct($file, $hasHeaders = true, $delimiter = ',', $enclosure = '"', $escape = "\0")
    {
        $this->headerNormalizer = new NullNormalizer();
        parent::__construct($file, $hasHeaders, $delimiter, $enclosure, $escape);
    }

    /**
     * @return string
     */
    public function getOpenMode()
    {
        return 'r';
    }

    /**
     * @param string $file
     *
     * @return Reader
     */
    public static function read($file)
    {
        $csv = new static($file);

        return $csv;
    }

    /**
     * @return Row
     */
    public function getHeaders()
    {
        if (null === $this->headers && $this->hasHeaders()) {
            $this->rewind();
        }

        return $this->headers;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getHeader($key)
    {
        if (!isset($this->headers[$key])) {
            return $key;
        }

        return $this->headers[$key];
    }

    /**
     * @param NormalizerInterface $headerNormalizer
     *
     * @return self
     */
    public function setHeaderNormalizer(NormalizerInterface $headerNormalizer)
    {
        $this->headerNormalizer = $headerNormalizer;

        return $this;
    }

    /**
     * @param mixed               $key
     * @param NormalizerInterface $normalizer Normalizer instance.
     *
     * @return self
     */
    public function addNormalizer($key, NormalizerInterface $normalizer)
    {
        $this->normalizers[$key] = $normalizer;

        return $this;
    }

    /**
     * @param array|\Traversable $normalizers
     *
     * @return self
     */
    public function addNormalizers($normalizers)
    {
        foreach ($normalizers as $key => $normalizer) {
            $this->addNormalizer($key, $normalizer);
        }

        return $this;
    }

    /**
     * @param mixed $key
     *
     * @return NormalizerInterface
     */
    public function getNormalizer($key)
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
     * Reads the next line in the CSV file
     * and returns a Row object from it.
     *
     * @return Row|null
     */
    protected function getCurrentRow()
    {
        $cells = $this->getDocument()->current();

        if (!\is_array($cells) || $cells == [null]) {
            return null;
        }

        if ($this->key() === 0 && $this->hasBom()) {
            $stripped = StringUtil::stripBom($cells[0], StringUtil::BOM_UTF8);

            if ($stripped !== $cells[0]) {
                $cells[0] = \trim($stripped, $this->getEnclosure());
            }
        }

        $row = new Row($cells, $this);

        return $row;
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return $this->row;
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->getDocument()->next();
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->getDocument()->key();
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        $this->row = $this->getCurrentRow();

        return $this->row instanceof Row;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->getDocument()->rewind();

        $dataOffset = $this->offset + $this->headerOffset;
        if ($this->hasHeaders()) {
            if ($this->headerOffset) {
                $this->getDocument()->seek($this->headerOffset);
            }

            // Set headers to null first so the header row is a zero-based array and can be used
            // to set the array keys of all other rows.
            $this->headers = null;
            $this->headers = $this->getCurrentRow();

            ++$dataOffset;
        }

        if ($dataOffset) {
            $this->getDocument()->seek($dataOffset);
        }
    }

    /**
     * @param string $defaultNormalizer
     *
     * @return Reader
     */
    public function setDefaultNormalizer($defaultNormalizer)
    {
        $this->defaultNormalizer = $defaultNormalizer;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultNormalizer()
    {
        return $this->defaultNormalizer;
    }

    /**
     * @param bool $bom
     *
     * @return Reader
     */
    public function setBom($bom)
    {
        $this->bom = $bom;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasBom()
    {
        return $this->bom;
    }

    /**
     * @param int $offset
     *
     * @return self
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $headerOffset
     *
     * @return self
     */
    public function setHeaderOffset($headerOffset)
    {
        $this->headerOffset = $headerOffset;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeaderOffset()
    {
        return $this->headerOffset;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];
        foreach ($this as $rowKey => $row) {
            $result[$rowKey] = $row->toArray();
        }

        return $result;
    }
}
