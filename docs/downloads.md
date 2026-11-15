# Downloads

## Build and Download a CSV File

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
