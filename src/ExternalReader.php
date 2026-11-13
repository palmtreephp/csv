<?php

declare(strict_types=1);

namespace Palmtree\Csv;

/**
 * Reader for CSV data from an external URL.
 * Streams data to a temporary file to avoid loading large files into memory.
 *
 * @example
 * $reader = new ExternalReader('https://example.com/data.csv');
 */
class ExternalReader extends Reader
{
    private ?string $tempFile = null;

    public function __construct(string $url, bool $hasHeaders = true)
    {
        // Create a temporary file to store the streamed data
        $tempFile = tempnam(sys_get_temp_dir(), 'palmtree_csv_');
        if ($tempFile === false) {
            throw new \RuntimeException('Failed to create temporary file');
        }

        $this->tempFile = $tempFile;

        // Open stream to the URL
        $remoteStream = fopen($url, 'r');
        if ($remoteStream === false) {
            $this->removeTempFile();
            throw new \RuntimeException("Failed to open URL: $url");
        }

        $tempFileResource = fopen($tempFile, 'w');

        if ($tempFileResource === false) {
            fclose($remoteStream);
            $this->removeTempFile();
            throw new \RuntimeException('Failed to open temporary file for writing');
        }

        // Stream the remote data to the temporary file without loading it all into memory
        if (stream_copy_to_stream($remoteStream, $tempFileResource) === false) {
            fclose($remoteStream);
            fclose($tempFileResource);
            $this->removeTempFile();
            throw new \RuntimeException('Failed to copy stream to temporary file');
        }

        fclose($remoteStream);
        fclose($tempFileResource);

        parent::__construct($tempFile, $hasHeaders);
    }

    #[\Override]
    public function __destruct()
    {
        parent::__destruct();

        $this->removeTempFile();
    }

    private function removeTempFile(): void
    {
        if ($this->tempFile !== null && is_file($this->tempFile)) {
            unlink($this->tempFile);
        }
    }
}
