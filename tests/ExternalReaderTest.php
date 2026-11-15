<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test;

use Palmtree\Csv\ExternalReader;
use Palmtree\Csv\Row\Row;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ExternalReaderTest extends TestCase
{
    /** @var resource|null */
    private static $server;
    private static string $baseUrl = '';

    public static function setUpBeforeClass(): void
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        \assert($socket !== false);
        $address = stream_socket_get_name($socket, false);
        fclose($socket);
        \assert($address !== false);

        $process = proc_open([\PHP_BINARY, '-S', $address, '-t', __DIR__ . '/fixtures'], [1 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']], $pipes);
        \assert($process !== false);

        self::$server = $process;
        self::$baseUrl = "http://$address";

        for ($i = 0; $i < 50; ++$i) {
            $connection = @fsockopen('127.0.0.1', (int)parse_url(self::$baseUrl, \PHP_URL_PORT));
            if ($connection !== false) {
                fclose($connection);

                return;
            }

            usleep(100_000);
        }

        self::fail('Test HTTP server did not start.');
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$server !== null) {
            proc_terminate(self::$server);
            proc_close(self::$server);
            self::$server = null;
        }
    }

    public function testReadsFromHttpUrl(): void
    {
        $reader = new ExternalReader(self::$baseUrl . '/products.csv');

        $this->assertInstanceOf(Row::class, $reader->getHeaders());
        $this->assertSame('Printer', $reader->toArray()[0]['name']);
    }

    public function testAcceptsStreamContext(): void
    {
        $context = stream_context_create(['http' => ['timeout' => 5]]);
        $reader = new ExternalReader(self::$baseUrl . '/products.csv', true, $context);

        $this->assertNotEmpty($reader->toArray());
    }

    public function testTempFileIsRemoved(): void
    {
        $reader = new ExternalReader(self::$baseUrl . '/products.csv');
        $reader->toArray();
        $tempFile = $reader->getFilePath();
        $this->assertFileExists($tempFile);
        // Rows reference their reader, so the reader is only destroyed once the cycle is collected.
        unset($reader);
        gc_collect_cycles();

        $this->assertFileDoesNotExist($tempFile);
    }

    #[DataProvider('disallowedUrlProvider')]
    public function testRejectsNonHttpUrls(string $url): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new ExternalReader($url);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function disallowedUrlProvider(): iterable
    {
        yield 'local path' => [__DIR__ . '/fixtures/products.csv'];
        yield 'file' => ['file://' . __DIR__ . '/fixtures/products.csv'];
        yield 'php' => ['php://filter/resource=' . __DIR__ . '/fixtures/products.csv'];
        yield 'phar' => ['phar://archive.phar/products.csv'];
        yield 'ftp' => ['ftp://example.com/products.csv'];
    }
}
