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
    private array $responseHeaders = [
        'Content-Type' => 'text/csv',
        'Content-Description' => 'File Transfer',
        'Content-Transfer-Encoding' => 'Binary',
        'Expires' => '0',
        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        'Pragma' => 'public',
    ];

    /**
     * @param iterable<string, string> $responseHeaders
     */
    public function __construct(private readonly string $filename, iterable $responseHeaders = [])
    {
        $this->addResponseHeaders($responseHeaders);
        $this->addResponseHeader('Content-Disposition', $this->getContentDisposition());

        parent::__construct('php://temp');
    }

    /**
     * @param array<array<array-key, mixed>> $data
     */
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
        $this->writeHeaders();

        if (!headers_sent()) {
            header(\sprintf('Content-Length: %s', $this->getDocument()->getSize()));

            foreach ($this->getResponseHeaders() as $key => $value) {
                header("$key: $value");
            }
        }

        $this->getDocument()->fseek(0);
        $this->getDocument()->fpassthru();
    }

    /**
     * @return array<string, string>
     */
    public function getResponseHeaders(): array
    {
        return $this->responseHeaders;
    }

    /**
     * @param iterable<string, string> $headers
     */
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

    /**
     * Builds a Content-Disposition header value with an ASCII-only filename for older clients
     * and an RFC 5987 encoded filename* for clients that support it.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc6266#section-4.3
     */
    private function getContentDisposition(): string
    {
        $fallback = preg_replace('/[^\x20-\x7E]|["\\\\]/u', '_', $this->filename)
            ?? (string)preg_replace('/[^\x20-\x7E]|["\\\\]/', '_', $this->filename);

        if ($fallback === $this->filename) {
            return \sprintf('attachment; filename="%s"', $fallback);
        }

        return \sprintf('attachment; filename="%s"; filename*=UTF-8\'\'%s', $fallback, rawurlencode($this->filename));
    }
}
