<?php

namespace Palmtree\Csv;

abstract class AbstractCsv
{
    /** @var string */
    protected $openMode;
    /** @var string */
    protected $file;
    /** @var bool */
    protected $hasHeaders;
    /** @var string */
    protected $delimiter;
    /** @var string */
    protected $enclosure;
    /** @var string */
    protected $escapeCharacter;
    /** @var CsvFileObject */
    protected $document;

    /**
     * AbstractCsv constructor.
     *
     * @param string $file       Path to CSV file.
     * @param bool   $hasHeaders Whether the CSV file contains headers.
     * @param string $delimiter  Cell delimiter. Default ',' (comma).
     * @param string $enclosure  Cell enclosure. Default '"' (double quote)
     * @param string $escape     Cell escape character. Default null byte.
     */
    public function __construct($file, $hasHeaders = true, $delimiter = ',', $enclosure = '"', $escape = "\0")
    {
        $this->setFile($file)
             ->setHasHeaders($hasHeaders)
             ->setDelimiter($delimiter)
             ->setEnclosure($enclosure)
             ->setEscapeCharacter($escape);
    }

    /**
     * AbstractCsv destructor.
     */
    public function __destruct()
    {
        $this->closeDocument();
    }

    /**
     * Creates a new SplFileObject instance.
     */
    public function createDocument()
    {
        $this->closeDocument();

        $document = new CsvFileObject($this->getFile(), $this->getOpenMode());

        $document->setFlags(
            CsvFileObject::READ_CSV |
            CsvFileObject::READ_AHEAD |
            CsvFileObject::DROP_NEW_LINE
        );

        $document->setCsvControl($this->getDelimiter(), $this->getEnclosure(), $this->getEscapeCharacter());

        $this->setDocument($document);
    }

    /**
     * Closes the document by setting our reference to null
     * to ensure its destructor is called.
     */
    public function closeDocument()
    {
        $this->document = null;
    }

    /**
     * @param CsvFileObject|null $document
     *
     * @return AbstractCsv
     */
    public function setDocument($document)
    {
        $this->closeDocument();

        $this->document = $document;

        return $this;
    }

    /**
     * @return CsvFileObject
     */
    public function getDocument()
    {
        if (!$this->document) {
            $this->createDocument();
        }

        return $this->document;
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
     * @return string
     */
    public function getOpenMode()
    {
        return $this->openMode;
    }

    /**
     * @return string
     */
    public function getEscapeCharacter()
    {
        return $this->escapeCharacter;
    }

    /**
     * @param string $escapeCharacter
     *
     * @return AbstractCsv
     */
    public function setEscapeCharacter($escapeCharacter)
    {
        $this->escapeCharacter = $escapeCharacter;

        return $this;
    }
}
