<?php

declare(strict_types=1);

namespace Palmtree\Csv;

class Downloader extends Writer
{
    /**
     * Default headers used to tell client the response is a downloadable,
     * non-cacheable file.
     *
     * @var array<string, string>
     */
    private $responseHeaders = [
        'Content-Type' => 'text/csv',
        'Content-Description' => 'File Transfer',
        'Content-Transfer-Encoding' => 'Binary',
        'Expires' => '0',
        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        'Pragma' => 'public',
    ];

    private string $filename;

    public function __construct(string $filename, iterable $responseHeaders = [])
    {
        $this->filename = $filename;

        $this->addResponseHeaders($responseHeaders);
        $this->addResponseHeader('Content-Disposition', \sprintf('attachment; filename="%s"', $this->getFilename()));

        parent::__construct('php://temp');
    }

    public static function download(string $filename, array $data): void
    {
        $downloader = new self($filename);
        $downloader->setData($data);
        $downloader->sendResponse();
    }

    /**
     * Attempts to send the file as a download to the client.
     */
    public function sendResponse(): void
    {
        $this->getDocument()->trimFinalLineEnding();

        if (!headers_sent()) {
            header(\sprintf('Content-Length: %s', $this->getDocument()->getSize()));

            foreach ($this->getResponseHeaders() as $key => $value) {
                header("$key: $value");
            }
        }

        $this->getDocument()->fseek(0);
        $this->getDocument()->fpassthru();
    }

    public function getResponseHeaders(): array
    {
        return $this->responseHeaders;
    }

    public function addResponseHeaders(iterable $headers = []): self
    {
        foreach ($headers as $key => $value) {
            $this->addResponseHeader($key, $value);
        }

        return $this;
    }

    public function addResponseHeader(string $key, string $value): void
    {
        $this->responseHeaders[$key] = $value;
    }

    public function removeResponseHeader(string $key): void
    {
        unset($this->responseHeaders[$key]);
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
