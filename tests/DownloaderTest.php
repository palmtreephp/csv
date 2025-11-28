<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test;

use Palmtree\Csv\Downloader;
use PHPUnit\Framework\TestCase;

class DownloaderTest extends TestCase
{
    public function testConstructorWithFilename(): void
    {
        $downloader = new Downloader('test-file.csv');

        $this->assertSame('test-file.csv', $downloader->getFilename());
    }

    public function testConstructorSetsDefaultResponseHeaders(): void
    {
        $downloader = new Downloader('test-file.csv');
        $headers = $downloader->getResponseHeaders();

        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertSame('text/csv', $headers['Content-Type']);
        $this->assertArrayHasKey('Content-Description', $headers);
        $this->assertSame('File Transfer', $headers['Content-Description']);
        $this->assertArrayHasKey('Content-Disposition', $headers);
        $this->assertStringContainsString('attachment; filename="test-file.csv"', $headers['Content-Disposition']);
    }

    public function testConstructorWithCustomResponseHeaders(): void
    {
        $customHeaders = [
            'X-Custom-Header' => 'custom-value',
            'Cache-Control' => 'no-cache',
        ];

        $downloader = new Downloader('test-file.csv', $customHeaders);
        $headers = $downloader->getResponseHeaders();

        $this->assertSame('custom-value', $headers['X-Custom-Header']);
        $this->assertSame('no-cache', $headers['Cache-Control']);
    }

    public function testAddResponseHeader(): void
    {
        $downloader = new Downloader('test-file.csv');
        $downloader->addResponseHeader('X-Test-Header', 'test-value');

        $headers = $downloader->getResponseHeaders();
        $this->assertSame('test-value', $headers['X-Test-Header']);
    }

    public function testAddResponseHeaders(): void
    {
        $downloader = new Downloader('test-file.csv');
        $newHeaders = [
            'X-Header-1' => 'value-1',
            'X-Header-2' => 'value-2',
        ];

        $result = $downloader->addResponseHeaders($newHeaders);

        $this->assertSame($downloader, $result);
        $headers = $downloader->getResponseHeaders();
        $this->assertSame('value-1', $headers['X-Header-1']);
        $this->assertSame('value-2', $headers['X-Header-2']);
    }

    public function testRemoveResponseHeader(): void
    {
        $downloader = new Downloader('test-file.csv');
        $downloader->addResponseHeader('X-Remove-Me', 'value');

        $this->assertArrayHasKey('X-Remove-Me', $downloader->getResponseHeaders());

        $downloader->removeResponseHeader('X-Remove-Me');

        $this->assertArrayNotHasKey('X-Remove-Me', $downloader->getResponseHeaders());
    }

    public function testSetData(): void
    {
        $downloader = new Downloader('test-file.csv');
        $data = [
            ['name' => 'John', 'age' => '30'],
            ['name' => 'Jane', 'age' => '25'],
        ];

        $result = $downloader->setData($data);

        $this->assertSame($downloader, $result);
        $this->assertGreaterThan(0, $downloader->getDocument()->getSize());
    }

    public function testGetResponseHeadersReturnsArray(): void
    {
        $downloader = new Downloader('test-file.csv');
        $headers = $downloader->getResponseHeaders();

        // Array type is already inferred from return type
        $this->assertNotEmpty($headers);
    }

    public function testDownloaderInheritedFromWriter(): void
    {
        $downloader = new Downloader('test.csv');
        $data = [
            ['name' => 'John', 'age' => '30'],
        ];

        // Verify setData (inherited from Writer) works
        $result = $downloader->setData($data);
        $this->assertSame($downloader, $result);
    }

    public function testAddResponseHeadersWithIterator(): void
    {
        $downloader = new Downloader('test.csv');
        $headers = [
            'X-Custom-1' => 'value-1',
            'X-Custom-2' => 'value-2',
        ];

        $result = $downloader->addResponseHeaders($headers);

        $this->assertSame($downloader, $result);
        $responseHeaders = $downloader->getResponseHeaders();
        $this->assertSame('value-1', $responseHeaders['X-Custom-1']);
        $this->assertSame('value-2', $responseHeaders['X-Custom-2']);
    }

    public function testRemoveResponseHeaderActuallyRemoves(): void
    {
        $downloader = new Downloader('test.csv');
        $downloader->addResponseHeader('X-Test-Header', 'test-value');

        $this->assertArrayHasKey('X-Test-Header', $downloader->getResponseHeaders());

        $downloader->removeResponseHeader('X-Test-Header');

        $this->assertArrayNotHasKey('X-Test-Header', $downloader->getResponseHeaders());
    }

    public function testDownloaderFilenameInContentDisposition(): void
    {
        $filename = 'my-export.csv';
        $downloader = new Downloader($filename);

        $headers = $downloader->getResponseHeaders();

        $this->assertStringContainsString($filename, $headers['Content-Disposition']);
    }

    public function testDownloaderCustomHeadersOverrideDefault(): void
    {
        $customHeaders = [
            'Content-Type' => 'text/plain',
        ];

        $downloader = new Downloader('test.csv', $customHeaders);
        $headers = $downloader->getResponseHeaders();

        $this->assertSame('text/plain', $headers['Content-Type']);
    }

    public function testDownloaderHasDefaultHeaders(): void
    {
        $downloader = new Downloader('test.csv');
        $headers = $downloader->getResponseHeaders();

        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertArrayHasKey('Content-Description', $headers);
        $this->assertArrayHasKey('Content-Transfer-Encoding', $headers);
        $this->assertArrayHasKey('Expires', $headers);
        $this->assertArrayHasKey('Cache-Control', $headers);
        $this->assertArrayHasKey('Pragma', $headers);
        $this->assertArrayHasKey('Content-Disposition', $headers);
    }

    public function testDownloaderWithData(): void
    {
        $downloader = new Downloader('users.csv');
        $data = [
            ['id' => '1', 'name' => 'John'],
            ['id' => '2', 'name' => 'Jane'],
        ];

        $downloader->setData($data);

        $contents = $downloader->getContents();
        $this->assertStringContainsString('John', $contents);
        $this->assertStringContainsString('Jane', $contents);
    }

    public function testDownloaderWithDelimiter(): void
    {
        $downloader = new Downloader('test.csv');
        $downloader->setDelimiter(';');
        $downloader->setHeaders(['col1', 'col2']);
        $downloader->addRow(['val1', 'val2']);

        $contents = $downloader->getContents();
        $this->assertStringContainsString(';', $contents);
    }

    public function testDownloaderGetFilename(): void
    {
        $expectedFilename = 'my-data.csv';
        $downloader = new Downloader($expectedFilename);

        $this->assertSame($expectedFilename, $downloader->getFilename());
    }
}
