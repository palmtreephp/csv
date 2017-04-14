<?php

namespace Palmtree\Csv;

class Downloader extends Writer
{
    protected $filename;

    public function __construct($filename = '', $delimiter = ',', $enclosure = '"', $hasHeaders = true)
    {
        if (! $filename) {
            $filename = time() . '.csv';
        }

        $this->setFilename($filename);

        parent::__construct('php://temp', $delimiter, $enclosure, $hasHeaders);
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     *
     * @return Downloader
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Attempts to send the file as download to the client.
     *
     * @throws \Exception
     */
    public function download()
    {
        if (headers_sent()) {
            throw new \Exception('Unable to start file download. Response headers already sent.');
        }

        header('Content-Type: application/octet-stream');
        header('Content-Description: File Transfer');
        header('Content-Transfer-Encoding: Binary');
        header(sprintf('Content-Disposition: attachment; filename="%s"', $this->getFilename()));

        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        rewind($this->getFileHandle());

        $output = stream_get_contents($this->getFileHandle());

        header('Content-Length: ' . mb_strlen($output));

        print $output;

        $this->closeFileHandle();
    }
}
