<?php

namespace Palmtree\Csv;

class Downloader extends Writer
{
    /**
     * Default headers used to tell client the response is
     * a downloadable, non-cacheable file.
     * @var array
     */
    public static $defaultResponseHeaders = [
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
    protected $responseHeaders;

    public function __construct($filename = '', $responseHeaders = [])
    {
        if (!$filename) {
            $filename = time() . '.csv';
        }

        $this->setFilename($filename);
        $this->setResponseHeaders($responseHeaders);

        parent::__construct('php://temp');
    }

    public function getResponseHeaders()
    {
        if (!$this->responseHeaders) {
            $this->setResponseHeaders();
        }

        return $this->responseHeaders;
    }

    public function setResponseHeaders($userHeaders = [])
    {
        $headers = static::$defaultResponseHeaders;

        $headers = array_replace($headers, $userHeaders);

        $this->responseHeaders = $headers;
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

        $headers = $this->getResponseHeaders();
        $body    = $this->getResponseBody();

        if (!empty($headers['Content-Length'])) {
            $headers['Content-Length'] = sprintf($headers['Content-Length'], mb_strlen($body));
        }

        if (isset($headers['Content-Disposition'])) {
            $headers['Content-Disposition'] = sprintf($headers['Content-Disposition'], $this->getFilename());
        }

        foreach ($headers as $key => $value) {
            header(sprintf('%s: %s', $key, $value));
        }

        print $body;

        $this->closeFileHandle();
    }

    protected function getResponseBody()
    {
        $this->trimTrailingLineEnding();

        rewind($this->getFileHandle());

        $body = stream_get_contents($this->getFileHandle());

        return $body;
    }
}
