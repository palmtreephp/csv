<?php

declare(strict_types=1);

namespace Palmtree\Csv;

abstract class AbstractCsvDocument
{
    protected bool $hasHeaders = true;
    protected string $delimiter = ',';
    protected string $enclosure = '"';
    protected string $escapeCharacter = "\0";
    private ?CsvFileObject $document = null;

    public function __construct(protected string $filePath, bool $hasHeaders = true)
    {
        $this->setHasHeaders($hasHeaders);
    }

    public function __destruct()
    {
        $this->closeDocument();
    }

    abstract protected function getOpenMode(): string;

    /**
     * Closes the document by setting our reference to null to ensure its destructor is called.
     */
    public function closeDocument(): void
    {
        $this->document = null;
    }

    public function setDocument(?CsvFileObject $document = null): self
    {
        $this->document = $document;

        return $this;
    }

    public function getDocument(): CsvFileObject
    {
        return $this->document ??= $this->createDocument();
    }

    public function hasDocument(): bool
    {
        return $this->document !== null;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * Sets the file path. Any open document is released so the new path is used on next access.
     */
    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;
        $this->document = null;

        return $this;
    }

    public function hasHeaders(): bool
    {
        return $this->hasHeaders;
    }

    public function setHasHeaders(bool $hasHeaders): self
    {
        $this->hasHeaders = $hasHeaders;

        return $this;
    }

    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    public function setDelimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;

        $this->document?->setCsvControl($this->delimiter, $this->enclosure, $this->escapeCharacter);

        return $this;
    }

    public function getEnclosure(): string
    {
        return $this->enclosure;
    }

    public function setEnclosure(string $enclosure): self
    {
        $this->enclosure = $enclosure;

        $this->document?->setCsvControl($this->delimiter, $this->enclosure, $this->escapeCharacter);

        return $this;
    }

    public function getEscapeCharacter(): string
    {
        return $this->escapeCharacter;
    }

    public function setEscapeCharacter(string $escapeCharacter): self
    {
        $this->escapeCharacter = $escapeCharacter;

        $this->document?->setCsvControl($this->delimiter, $this->enclosure, $this->escapeCharacter);

        return $this;
    }

    private function createDocument(): CsvFileObject
    {
        $document = new CsvFileObject($this->filePath, $this->getOpenMode());

        $document->setFlags(\SplFileObject::READ_CSV);
        $document->setCsvControl($this->delimiter, $this->enclosure, $this->escapeCharacter);

        return $document;
    }
}
