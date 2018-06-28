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
    protected $defaultNormalizer = NullNormalizer::class;
    /** @var string */
    protected $openMode = 'r';
    /** @var NormalizerInterface[] */
    protected $normalizers = [];
    /** @var Row */
    protected $headers;
    /** @var Row */
    protected $row;
    /** @var bool */
    protected $bom = false;

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
        if (is_null($this->headers) && $this->hasHeaders()) {
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
     * @param mixed               $key
     * @param NormalizerInterface $normalizer Normalizer instance.
     *
     * @return $this
     */
    public function addNormalizer($key, NormalizerInterface $normalizer)
    {
        $this->normalizers[$key] = $normalizer;

        return $this;
    }

    /**
     * @param array|\Traversable $normalizers
     *
     * @return $this
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

        if (!is_array($cells)) {
            return null;
        }

        if ($this->key() === 0 && $this->hasBom()) {
            $stripped = StringUtil::stripBom($cells[0], StringUtil::BOM_UTF8);

            if ($stripped !== $cells[0]) {
                $cells[0] = trim($stripped, $this->getEnclosure());
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

        if ($this->hasHeaders()) {
            $this->headers = $this->getCurrentRow();
            $this->next();
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

}
