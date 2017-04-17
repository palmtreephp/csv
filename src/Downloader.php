<?php

namespace Palmtree\Csv;

class Downloader extends Writer
{
    /**
     * Default headers used to tell client the response is
     * a downloadable, non-cacheable file.
     * @var array
     */
    protected $responseHeaders = [
        'Content-Type'              => 'application/octet-stream',
        'Content-Description'       => 'File Transfer',
        'Content-Transfer-Encoding' => 'Binary',
        'Content-Disposition'       => 'attachment; filename="%s"',
        'Expires'                   => '0',
        'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
        'Pragma'                    => 'public',
        'Content-Length'            => '%s',
    ];

    protected $filename;

    public function __construct($filename = '', $responseHeaders = [])
    {
        if (!$filename) {
            $filename = time() . '.csv';
        }

        $this->setFilename($filename);
        $this->addResponseHeaders($responseHeaders);

        parent::__construct('php://temp');
    }

    public static function download($file, $data)
    {
        $downloader = new static($file);
        $downloader->setData($data);
        $downloader->sendResponse();
    }

    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    public function addResponseHeaders($userHeaders = [])
    {
        foreach ($userHeaders as $key => $value) {
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

    /**
     * Attempts to send the file as a download to the client.
     *
     * @throws \Exception
     */
    public function sendResponse()
    {
        $body = $this->getResponseBody();

        if (!headers_sent()) {
            $headers = $this->getResponseHeaders();

            foreach ($headers as $key => $value) {
                if ($key === 'Content-Length') {
                    $value = sprintf($value, mb_strlen($body));
                } elseif ($key === 'Content-Disposition') {
                    $value = sprintf($value, $this->getFilename());
                }

                header(sprintf('%s: %s', $key, $value));
            }
        }

        print $body;

        $this->closeDocument();
    }

    protected function getResponseBody()
    {
        $this->trimTrailingLineEnding();

        rewind($this->getFileHandle());

        $body = stream_get_contents($this->getFileHandle());

        return $body;
    }
}
