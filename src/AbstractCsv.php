<?php

namespace Palmtree\Csv;

abstract class AbstractCsv
{
    /** @var string Path to CSV file. */
    protected $file;
    /** @var bool Whether the CSV file contains headers */
    protected $hasHeaders = true;
    /** @var string Cell delimiter. Default ',' (comma) */
    protected $delimiter = ',';
    /** @var string Cell enclosure. Default '"' (double quote) */
    protected $enclosure = '"';
    /** @var string Cell escape character. Default null byte */
    protected $escapeCharacter = "\0";
    /** @var CsvFileObject */
    protected $document;

    /**
     * @param string $file Path to CSV file.
     */
    public function __construct(string $file)
    {
        $this->setFile($file);
    }

    public function __destruct()
    {
        $this->closeDocument();
    }

    abstract public function getOpenMode(): string;

    public function createDocument(): void
    {
        $this->closeDocument();

        $document = new CsvFileObject($this->file, $this->getOpenMode());

        $document->setFlags(CsvFileObject::READ_CSV);
        $document->setCsvControl($this->delimiter, $this->enclosure, $this->escapeCharacter);

        $this->setDocument($document);
    }

    /**
     * Closes the document by setting our reference to null to ensure its destructor is called.
     */
    public function closeDocument(): void
    {
        $this->document = null;
    }

    public function setDocument(CsvFileObject $document = null): self
    {
        $this->closeDocument();

        $this->document = $document;

        return $this;
    }

    public function getDocument(): CsvFileObject
    {
        if (!$this->document) {
            $this->createDocument();
        }

        return $this->document;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function hasHeaders(): bool
    {
        return $this->hasHeaders;
    }

    public function setHasHeaders(bool $hasHeaders): self
    {
        $this->hasHeaders = (bool)$hasHeaders;

        return $this;
    }

    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    public function setDelimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function getEnclosure(): string
    {
        return $this->enclosure;
    }

    public function setEnclosure(string $enclosure): self
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    public function getEscapeCharacter(): string
    {
        return $this->escapeCharacter;
    }

    public function setEscapeCharacter(string $escapeCharacter): self
    {
        $this->escapeCharacter = $escapeCharacter;

        return $this;
    }
}
