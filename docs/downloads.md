# Downloads

`Downloader` is a [`Writer`](writing.md) that sends the CSV to the browser as a file download. It writes to a
`php://temp` stream, which is kept in memory up to 2 MB and moves to a temporary file after that.

## Build and Download a CSV File

`Downloader::download()` sends the headers and the file in one call:

```php
$people   = [];
$people[] = [
    'name'   => 'Alice',
    'age'    => '24',
    'gender' => 'Female',
];
$people[] = [
    'name'   => 'Bob',
    'age'    => '28',
    'gender' => 'Male',
];

Downloader::download('filename.csv', $people);
```

To build the file first, create a `Downloader` and call `sendResponse()` when it's ready. It has all of `Writer`'s
methods:

```php
$csv = new Downloader('people.csv');
$csv->setEscapeFormulas(true);

foreach ($people as $person) {
    $csv->addRow($person);
}

$csv->sendResponse();
exit;
```

`sendResponse()` sends the response headers, including `Content-Length`, then the file. If output has already started,
it can't send headers, so it only sends the file.

## Response Headers

By default, the response is sent with these headers:

| Header | Value |
|---|---|
| `Content-Type` | `text/csv; charset=utf-8` |
| `Content-Disposition` | `attachment; filename="people.csv"` |
| `Content-Description` | `File Transfer` |
| `Content-Transfer-Encoding` | `Binary` |
| `Expires` | `0` |
| `Cache-Control` | `must-revalidate, post-check=0, pre-check=0` |
| `Pragma` | `public` |

Filenames with quotes or non-ASCII characters get an ASCII fallback in `filename`, plus the full name in an
[RFC 5987](https://datatracker.ietf.org/doc/html/rfc5987) encoded `filename*`, so they download with the right name.

Pass extra headers to the constructor, or change them later:

```php
$csv = new Downloader('people.csv', ['X-Robots-Tag' => 'noindex']);

$csv->addResponseHeader('Cache-Control', 'no-store');
$csv->addResponseHeaders(['Content-Type' => 'text/csv; charset=windows-1252']);
$csv->removeResponseHeader('Pragma');

$csv->getResponseHeaders(); // All the headers to send
```

## Framework Responses

`Downloader::sendResponse()` and `Downloader::download()` call `header()` and write straight to the output. To return a
response from a framework instead, use the downloader's response headers and `writeTo()`, which copies the CSV to a
stream in chunks.

Symfony:

```php
use Symfony\Component\HttpFoundation\StreamedResponse;

$csv = new Downloader('people.csv');
$csv->setData($people);

return new StreamedResponse(
    fn () => $csv->writeTo(fopen('php://output', 'w')),
    200,
    $csv->getResponseHeaders(),
);
```

PSR-7, with any PSR-17 response and stream factory:

```php
$csv = new Downloader('people.csv');
$csv->setData($people);

$stream = fopen('php://temp', 'w+');
$csv->writeTo($stream);
rewind($stream);

$response = $responseFactory->createResponse()
    ->withBody($streamFactory->createStreamFromResource($stream));

foreach ($csv->getResponseHeaders() as $name => $value) {
    $response = $response->withHeader($name, $value);
}

return $response;
```
