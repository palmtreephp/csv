<?php

namespace Palmtree\Csv;

abstract class AbstractCsv
{
    /** @var string */
    protected $fopenMode;
    /** @var string */
    protected $file;
    /** @var bool */
    protected $hasHeaders;
    /** @var string */
    protected $delimiter;
    /** @var string */
    protected $enclosure;
    /** @var \SplFileObject */
    protected $document;

    /**
     * AbstractCsv constructor.
     *
     * @param string $file       Path to CSV file.
     * @param bool   $hasHeaders Whether the CSV file contains headers.
     * @param string $delimiter  Cell delimiter. Default ',' (comma).
     * @param string $enclosure  Cell enclosure. Default '"' (double quote)
     */
    public function __construct($file, $hasHeaders = true, $delimiter = ',', $enclosure = '"')
    {
        $this->setFile($file)
             ->setHasHeaders($hasHeaders)
             ->setDelimiter($delimiter)
             ->setEnclosure($enclosure);
    }

    /**
     * Reader destructor.
     */
    public function __destruct()
    {
        $this->closeDocument();
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @param string $openMode
     */
    public function createDocument($openMode = '')
    {
        if (!$openMode) {
            $openMode = $this->fopenMode;
        }

        $document = new \SplFileObject($this->getFile(), $openMode);

        $document->setFlags(
            \SplFileObject::READ_CSV |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::DROP_NEW_LINE
        );

        $document->setCsvControl($this->getDelimiter(), $this->getEnclosure());

        $this->setDocument($document);
    }

    /**
     *
     */
    public function closeDocument()
    {
        $this->setDocument(null);
    }

    /**
     * @return bool
     */
    public function hasHeaders()
    {
        return $this->hasHeaders;
    }

    /**
     * @param bool $hasHeaders
     *
     * @return AbstractCsv
     */
    public function setHasHeaders($hasHeaders)
    {
        $this->hasHeaders = (bool)$hasHeaders;

        return $this;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     *
     * @return AbstractCsv
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * @param string $enclosure
     *
     * @return AbstractCsv
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * @param \SplFileObject|null $document
     *
     * @return AbstractCsv
     */
    public function setDocument($document)
    {
        // Ensure any previous instance is destroyed.
        $this->document = null;

        $this->document = $document;

        return $this;
    }

    /**
     * @return \SplFileObject
     */
    public function getDocument()
    {
        return $this->document;
    }
}
