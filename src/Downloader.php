<?php

namespace Palmtree\Csv;

class Downloader extends Writer
{
    /**
     * Default headers used to tell client the response is a downloadable,
     * non-cacheable file.
     *
     * @var array
     */
    private $responseHeaders = [
        'Content-Type'              => 'text/csv',
        'Content-Description'       => 'File Transfer',
        'Content-Transfer-Encoding' => 'Binary',
        'Expires'                   => '0',
        'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
        'Pragma'                    => 'public',
    ];

    /** @var string */
    private $filename;

    public function __construct($filename, $responseHeaders = [])
    {
        $this->setFilename($filename);

        $this->addResponseHeader('Content-Disposition', \sprintf('attachment; filename="%s"', $this->getFilename()));
        $this->addResponseHeaders($responseHeaders);

        parent::__construct('php://temp');
    }

    public static function download($file, $data)
    {
        $downloader = new static($file);
        $downloader->setData($data);
        $downloader->sendResponse();
    }

    /**
     * Attempts to send the file as a download to the client.
     *
     * @throws \Exception
     */
    public function sendResponse()
    {
        $this->getDocument()->trimFinalLineEnding();

        if (!\headers_sent()) {
            \header(\sprintf('Content-Length: %s', $this->getDocument()->getSize()));

            foreach ($this->getResponseHeaders() as $key => $value) {
                \header(\sprintf('%s: %s', $key, $value));
            }
        }

        $this->getDocument()->fseek(0);
        $this->getDocument()->fpassthru();
    }

    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    public function addResponseHeaders($headers = [])
    {
        foreach ($headers as $key => $value) {
            $this->addResponseHeader($key, $value);
        }

        return $this;
    }

    public function addResponseHeader($key, $value)
    {
        $this->responseHeaders[$key] = $value;
    }

    public function removeResponseHeader($key)
    {
        unset($this->responseHeaders[$key]);
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
}
