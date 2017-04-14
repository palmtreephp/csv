<?php

namespace Palmtree\Csv;

abstract class AbstractCsv
{
    /** @var string */
    protected $fopenMode;
    /** @var resource */
    protected $fileHandle;
    /** @var string */
    protected $file;
    /** @var bool */
    protected $hasHeaders;
    /** @var string */
    protected $delimiter;
    /** @var string */
    protected $enclosure;

    /**
     * AbstractCsv constructor.
     *
     * @param string $file       Path to CSV file to parse.
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
        $this->closeFileHandle();
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
     * @return resource|null
     */
    public function getFileHandle()
    {
        return $this->fileHandle;
    }

    /**
     * @param resource $fileHandle
     *
     * @return $this
     */
    public function setFileHandle($fileHandle)
    {
        $this->fileHandle = $fileHandle;

        return $this;
    }

    /**
     * @param string $mode
     */
    public function createFileHandle($mode = '')
    {
        $this->closeFileHandle();

        if (!$mode) {
            $mode = $this->fopenMode;
        }

        $handle = @fopen($this->getFile(), $mode);

        if (!$handle) {
            throw new \InvalidArgumentException(sprintf('Could not open "%s" in mode "%s"', $this->getFile(), $mode));
        }

        $this->setFileHandle($handle);
    }

    /**
     *
     */
    public function closeFileHandle()
    {
        if ($this->getFileHandle()) {
            @fclose($this->getFileHandle());
        }

        $this->setFileHandle(null);
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
}
